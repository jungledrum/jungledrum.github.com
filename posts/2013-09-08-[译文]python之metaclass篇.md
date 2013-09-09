**原文：[What is a metaclass in Python?](http://stackoverflow.com/questions/100003/what-is-a-metaclass-in-python)**

##1. class是对象

在理解元类（metaclass），你需要掌握类（class）。Python从Smalltalk语言中借鉴了关于类的奇特特性。

在大部分语言里，类只是描述如何产生对象的代码。这在Python中也是正确的。

    >>> class ObjectCreator(object):
    ...       pass
    ... 

    >>> my_object = ObjectCreator()
    >>> print my_object
    <__main__.ObjectCreator object at 0x8974f2c>

但在Python里，类不仅仅只是这些。类还是对象。

当你使用关键词class时，Python就会执行它并创建一个对象。

    >>> class ObjectCreator(object):
    ...       pass
    ... 

在内存中创建一个名称为ObjectCreator的对象

这个对象能够创建新的对象，这就是它为什么叫类。

但是它依然是一个对象，因此：

- 你可以把它赋给一个变量
- 你可以复制它
- 你可以给它添加属性
- 你可以把它当作一个函数的参数

例如：

    >>> print ObjectCreator # you can print a class because it's an object
    <class '__main__.ObjectCreator'>
    >>> def echo(o):
    ...       print o
    ... 
    >>> echo(ObjectCreator) # you can pass a class as a parameter
    <class '__main__.ObjectCreator'>
    >>> print hasattr(ObjectCreator, 'new_attribute')
    False
    >>> ObjectCreator.new_attribute = 'foo' # you can add attributes to a class
    >>> print hasattr(ObjectCreator, 'new_attribute')
    True
    >>> print ObjectCreator.new_attribute
    foo
    >>> ObjectCreatorMirror = ObjectCreator # you can assign a class to a variable
    >>> print ObjectCreatorMirror.new_attribute
    foo
    >>> print ObjectCreatorMirror()
    <__main__.ObjectCreator object at 0x8997b4c>

##2. 动态创建类 

既然类也是对象，那么你就可以像创建其它对象一样创建它。

首先，你可以在一个函数里用关键词class创建类

    >>> def choose_class(name):
    ...     if name == 'foo':
    ...         class Foo(object):
    ...             pass
    ...         return Foo # return the class, not an instance
    ...     else:
    ...         class Bar(object):
    ...             pass
    ...         return Bar
    ...     
    >>> MyClass = choose_class('foo') 
    >>> print MyClass # the function returns a class, not an instance
    <class '__main__.Foo'>
    >>> print MyClass() # you can create an object from this class
    <__main__.Foo object at 0x89c6d4c>

但是，这看起来并不怎么动态，因为你还要在函数里把整个类写出来

既然类是对象，那么它一定可以被其它东西产生。

当你使用关键词class时，Python自动创建一个对象。但像Python中的大多数情况一样，提供了一种方式让你可以手动创建它。

还记得type函数吗？这个函数可以让你知道一个对象的类型。

    >>> print type(1)
    <type 'int'>
    >>> print type("1")
    <type 'str'>
    >>> print type(ObjectCreator)
    <type 'type'>
    >>> print type(ObjectCreator())
    <class '__main__.ObjectCreator'>

其实，type还有一个完全不同的功能，它可以创建类。type可以接受一个描述类的参数，然后返回一个类。

(一个函数因为参数不同而有不同的功能，这看起来有点奇怪，这个问题主要是为了向后兼容)

type的工作方式是这样的：

    type(name of the class, 
       tuple of the parent class (for inheritance, can be empty), 
       dictionary containing attributes names and values)
例如：

    >>> class MyShinyClass(object):
    ...       pass
可以这样手动创建

    >>> MyShinyClass = type('MyShinyClass', (), {}) # returns a class object
    >>> print MyShinyClass
    <class '__main__.MyShinyClass'>
    >>> print MyShinyClass() # create an instance with the class
    <__main__.MyShinyClass object at 0x8997cec>

你会注意到“MyShinyClass”既是类名又是类的引用。它们可以是不同的，不过这里没有必要复杂化。

type接受一个字典来定义类的属性。

    >>> class Foo(object):
    ...       bar = True
可以写成：

    >>> Foo = type('Foo', (), {'bar':True})

    >>> print Foo
    <class '__main__.Foo'>
    >>> print Foo.bar
    True
    >>> f = Foo()
    >>> print f
    <__main__.Foo object at 0x8a9b84c>
    >>> print f.bar
    True
你也可以继承它，

    >>>   class FooChild(Foo):
    ...         pass

    >>> FooChild = type('FooChild', (Foo,), {})
    >>> print FooChild
    <class '__main__.FooChild'>
    >>> print FooChild.bar # bar is inherited from Foo
    True
最后你可能想要给它添加一个方法。只需要定义一个函数，然后当作属性赋值给类。

    >>> def echo_bar(self):
    ...       print self.bar
    ... 
    >>> FooChild = type('FooChild', (Foo,), {'echo_bar': echo_bar})
    >>> hasattr(Foo, 'echo_bar')
    False
    >>> hasattr(FooChild, 'echo_bar')
    True
    >>> my_foo = FooChild()
    >>> my_foo.echo_bar()
    True

这就是你使用关键词class时候，Python做的事情，而且它还使用了元类。

##3. 什么是元类（metaclass）

元类是用来创建类的东西。

你定义类是为了创建对象，是吧？

但是我们知道Python的类就是对象。

元类就是创建这些对象的东西。它们是类的类。

    MyClass = MetaClass()
    MyObject = MyClass()

你已经知道type可以让你这么做：

    MyClass = type('MyClass', (), {})
这是因为函数type事实上就是一个元类。type是Python创建所有类的元类。

你可能奇怪为什么是个小写，而不是Type？

我想这是为了保持一致性，就像str是创建字符串对象的类，int是创建整数的类，type是创建类的类。

通过检测__class__属性，你可以看到。

在Python里，一切皆是对象。包括整数，字符串，函数和类。它们都是对象，而且它们都是从类里创建的。

    >>> age = 35
    >>> age.__class__
    <type 'int'>
    >>> name = 'bob'
    >>> name.__class__
    <type 'str'>
    >>> def foo(): pass
    >>> foo.__class__
    <type 'function'>
    >>> class Bar(object): pass
    >>> b = Bar()
    >>> b.__class__
    <class '__main__.Bar'>

那么，__class__的__class__是什么呢？

    >>> age.__class__.__class__
    <type 'type'>
    >>> name.__class__.__class__
    <type 'type'>
    >>> foo.__class__.__class__
    <type 'type'>
    >>> b.__class__.__class__
    <type 'type'>
元类就是创建类的东西。

你可以叫它‘类工厂’。

type是Python内置的元类，你也可以创建你自己的元类。

##4. __metaclass__属性

你创建类的时候可以添加一个__metaclass_属性：

    class Foo(object):
    __metaclass__ = something...
    [...]
如果你这样做，Python会使用metaclass来创建Foo

你写了`class Foo(object)`，但是类Foo还没有在内存中创建。

Python会先在类定义中找__metaclass__。如果找到它，就会用它来创建类Foo。如果没有找到，就会用type来创建类。

当你写下：

    class Foo(Bar):
      pass
Python会这么做：

Foo里有属性__metaclass__吗？

如果有，会使用__metaclass__在内存中创建一个名称为Foo的类对象。

如果没有找到__metaclass__，它会在父类Bar里找。

如果父类里都没有，它会在模块里找。

要是还没找到，就会用type来创建类对象。

现在的问题是，__metaclass__的值可以是什么？

答案就是：一切可以创建类的东西。

什么可以创建类？type，或者它的子类或者使用它的。

##5. 自定义元类
元类的主要意图是当创建类的时候自动改变这个类。

这在创建API的时候会很有用，你会根据上下文创建适合的类。

设想一个比较笨的例子，你想要所有你模块中的类的属性都是大写。这里有很多中方式，在模块中设置__metaclass__就是一种方法。

这样，所有这个模块创建的类都会使用这个元类，我们只要告诉这个元类把所有的属性转换为大写就可以了。

幸运的是，__metaclass__可以是任意可以被调用的（callbale），并不一定需要是一个正式的类（就是用关键词class定义的）。

我们先用一个使用函数的简单例子。

    # the metaclass will automatically get passed the same argument
    # that you usually pass to `type`
    def upper_attr(future_class_name, future_class_parents, future_class_attr):
      """
        Return a class object, with the list of its attribute turned 
        into uppercase.
      """

      # pick up any attribute that doesn't start with '__' and uppercase it
      uppercase_attr = {}
      for name, val in future_class_attr.items():
          if not name.startswith('__'):
              uppercase_attr[name.upper()] = val
          else:
              uppercase_attr[name] = val

      # let `type` do the class creation
      return type(future_class_name, future_class_parents, uppercase_attr)

    __metaclass__ = upper_attr # this will affect all classes in the module

    class Foo(): # global __metaclass__ won't work with "object" though
      # but we can define __metaclass__ here instead to affect only this class
      # and this will work with "object" children
      bar = 'bip'

    print hasattr(Foo, 'bar')
    # Out: False
    print hasattr(Foo, 'BAR')
    # Out: True

    f = Foo()
    print f.BAR
    # Out: 'bip'

现在，我们再来用类作为元类：

    # remember that `type` is actually a class like `str` and `int`
    # so you can inherit from it
    class UpperAttrMetaclass(type): 
        # __new__ is the method called before __init__
        # it's the method that creates the object and returns it
        # while __init__ just initializes the object passed as parameter
        # you rarely use __new__, except when you want to control how the object
        # is created.
        # here the created object is the class, and we want to customize it
        # so we override __new__
        # you can do some stuff in __init__ too if you wish
        # some advanced use involves overriding __call__ as well, but we won't
        # see this
        def __new__(upperattr_metaclass, future_class_name, 
                    future_class_parents, future_class_attr):

            uppercase_attr = {}
            for name, val in future_class_attr.items():
                if not name.startswith('__'):
                    uppercase_attr[name.upper()] = val
                else:
                    uppercase_attr[name] = val

            return type(future_class_name, future_class_parents, uppercase_attr)

但这并不是真正的OOP。我们直接调用的type，而没有覆盖父类的__new__。我们再来修改一下：

    class UpperAttrMetaclass(type): 

        def __new__(upperattr_metaclass, future_class_name, 
                    future_class_parents, future_class_attr):

            uppercase_attr = {}
            for name, val in future_class_attr.items():
                if not name.startswith('__'):
                    uppercase_attr[name.upper()] = val
                else:
                    uppercase_attr[name] = val

            # reuse the type.__new__ method
            # this is basic OOP, nothing magic in there
            return type.__new__(upperattr_metaclass, future_class_name, 
                                future_class_parents, uppercase_attr)
你可能已经注意到了upperattr_metaclass参数。它并没有什么特殊的：一个方法总是接受当前的实例作为第一个参数。就像一般方法里的self。

当然，我这里的命名有点长了，真是的环境中可能是这样：

    class UpperAttrMetaclass(type): 

        def __new__(cls, clsname, bases, dct):

            uppercase_attr = {}
            for name, val in dct.items():
                if not name.startswith('__'):
                    uppercase_attr[name.upper()] = val
                else:
                    uppercase_attr[name] = val

            return type.__new__(cls, clsname, bases, uppercase_attr)
我们可以使用super来让它更清晰一点：

    class UpperAttrMetaclass(type): 

        def __new__(cls, clsname, bases, dct):

            uppercase_attr = {}
            for name, val in dct.items():
                if not name.startswith('__'):
                    uppercase_attr[name.upper()] = val
                else:
                    uppercase_attr[name] = val

            return super(UpperAttrMetaclass, cls).__new__(cls, clsname, bases, uppercase_attr)
这是就是全部，关于元类，没有什么东西了。

**原文：[What is a metaclass in Python?](http://stackoverflow.com/questions/100003/what-is-a-metaclass-in-python)**

使用了元类的代码很复杂并不是应为元类，而是你通常使用元类来扭曲一些依赖内省(introspection)，操作继承，变量，类如`__dict__`，等等。

事实上，元类在玩黑魔法的时候很有用，因此很复杂，但它本身是简单的：

- 拦截类的创建
- 修改类
- 返回修改的类

##6. 为什么你应该用类而不是函数?

既然__metaclass__可以接受任何可调用的，为什么还要用类，因为它更复杂吗？

这里有若干原因：

- 意图更清晰。当你读到`UpperAttrMetaclass(type)`，你知道接下来是什么。
- 你可以使用OOP。元类可以继承元类，覆盖父类方法。元类甚至可以使用元类。
- 你可以更好的组织你的代码。你使用元类的时候不会像上面的例子一样是一些没用的代码。通常会很复杂，把若干方法组织到一个类里会让代码更容易阅读。
- 你可以使用钩子`__new__`,`__init__`,`__call__`。这可以让你做一些不同的东西。即使通常你可以在`__new__`里做完所有的事情，但是有些人会更乐意用`__init__`。

##7. 为什么你使用元类的时候会掉进地狱?
最大的问题是，为什么你要使用一些明显容易产生错误的特性?

通常你并不要它：

> 元类是一个高级的魔法，超过99%的人们会担心它。如果你犹豫是否应该使用它，那么你不需要（那些需要使用它的人们很明确的知道自己需要它，而不需要一个理由）

> Python Guru Tim Peters

元类的主要用途是创建API。一个典型的例子就是Django的ORM。

它允许你这样定义：

    class Person(models.Model):
    name = models.CharField(max_length=30)
    age = models.IntegerField()

但是你这样做：

    guy = Person(name='bob', age='35')
    print guy.age
    
它不会返回一个`IntegerField`对象。它会返回一个`int`,并且你甚至可以直接从数据库里得到它。

这可能是因为`models.Model`定义了`__metaclass__`，并且使用了一些魔法将你定义的`Person`转为成了数据库字段的钩子。

Django通过简单的API和元类使一些复杂的钩子看起来很简单。

##8. 结语 
首先，你知道了类是可以创建实例的对象。

事实上，类也是实例，是元类的实例。

    >>> class Foo(object): pass
    >>> id(Foo)
    142630324
一切都是对象，或者是类的实例，或者是元类的实例。

除了type。

type其实是它自己的元类。你不能用纯Python重新创造这个东西，这是在Python语言实现层上的。

其次，元类是复杂的。你可能不会想在简单的类修改的时候用它。你可以使用两种技术来改变类：

- 猴子补丁
- 类装饰器

99%的情况你想要修改类的时候，你可以用上面这两个东西。

不过99%的情况下，你根本不需要修改类 :-)
