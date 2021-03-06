起因是我想在程序的不同地方使用同一个变量，而又想在不同线程里维护一个私有副本。和pp同学讨论了一下就是维护一个全局变量，里面保存一个线程标识符和对应的值，每个线程取的时候只取自己对应标识符下的变量。后来看了一下werkzeug中的threadlocal就是这么实现的。再后来看webpy中的代码，发现也有个threadlocal，但是实现方法却不同，它是将变量直接存在每个线程对象里，也是个巧妙的方法。然后就想到werkzeug中的实现会不会导致内存泄漏？

 werkzeug中的实现

    class Local(object):

        def __init__(self):
            object.__setattr__(self, '__storage__', {})
            object.__setattr__(self, '__ident_func__', get_ident)

        def __getattr__(self, name):
            try:
                return self.__storage__[self.__ident_func__()][name]
            except KeyError:
                raise AttributeError(name)

        def __setattr__(self, name, value):
            ident = self.__ident_func__()
            storage = self.__storage__
            try:
                storage[ident][name] = value
            except KeyError:
                storage[ident] = {name: value}

webpy中的实现

    class threadlocal(object):

        def __getattribute__(self, name):
            if name == "__dict__":
                return threadlocal._getd(self)
            else:
                try:
                    return object.__getattribute__(self, name)
                except AttributeError:
                    try:
                        return self.__dict__[name]
                    except KeyError:
                        raise AttributeError, name
                
        def __setattr__(self, name, value):
            self.__dict__[name] = value
        
        def _getd(self):
            t = threading.currentThread()
            if not hasattr(t, '_d'):
                t._d = {}
            
            _id = id(self)
            if _id not in t._d:
                t._d[_id] = {}
            return t._d[_id]
