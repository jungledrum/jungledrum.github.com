为了理解yield，你必须理解什么是generator，而为了理解generator，先要理解iterable

##1. 可迭代（Iterable）

当你创建一个list的时候，你可以依次访问它的子项，这就叫迭代：

    >>> mylist = [1, 2, 3]
    >>> for i in mylist:
    ...    print(i)
    1
    2
    3

mylist是可迭代的。当你使用list comprehension时，你创建了一个list，也就是一个可迭代的对象。

    >>> mylist = [x*x for x in range(3)]
    >>> for i in mylist:
    ...    print(i)
    0
    1
    4

当你可以使用“for... in...”语句的时候，它就是一个可迭代对象，如：lists，strings，files等。这些可迭代对象用起来很方便，但是它会把所有值都存在内存里。

##2. 产生器（Generators）

Generators就是迭代器，但是你只能遍历一次。这是因为它并不会把所有值都存在内存里，它们在运行的时候产生下一个值。

    >>> mygenerator = (x*x for x in range(3))
    >>> for i in mygenerator:
    ...    print(i)
    0
    1
    4

使用()替换[]会产生一样的效果，区别在于你只能遍历一次，你不能再使用`for i in mygenerator`。

##3.Yield

Yield是一个关键词，用起来很像return，返回一个generator

    >>> def createGenerator():
    ...    mylist = range(3)
    ...    for i in mylist:
    ...        yield i*i
    ...
    >>> mygenerator = createGenerator() # create a generator
    >>> print(mygenerator) # mygenerator is an object!
    <generator object createGenerator at 0xb7555c34>
    >>> for i in mygenerator:
    ...     print(i)
    0
    1
    4

当一个函数会返回一个很大的集合，并且你只会使用一次，你就可以用上面这种方法。

为了掌握yield，你必须理解当你调用这个函数的时候，这个函数里的代码并不会运行，它会返回一个generator对象。
然后你可以使用for语句来遍历这个generator对象

现在进入困难的部分：

for语句第一次调用generator对象的时候，它会从头执行你的代码一直到yield语句，然后返回一个值，下一次调用返回下一个值，一直到没有值。

generator会认为是空的，一但这个函数没有碰到yield。这有可能是循环结束，也有可能是你的if/else没写对。

##4. generator的高级用法

###4.1 控制一个generator枯竭

    >>> class Bank(): # let's create a bank, building ATMs
    ...    crisis = False
    ...    def create_atm(self):
    ...        while not self.crisis:
    ...            yield "$100"
    >>> hsbc = Bank() # when everything's ok the ATM gives you as much as you want
    >>> corner_street_atm = hsbc.create_atm()
    >>> print(corner_street_atm.next())
    $100
    >>> print(corner_street_atm.next())
    $100
    >>> print([corner_street_atm.next() for cash in range(5)])
    ['$100', '$100', '$100', '$100', '$100']
    >>> hsbc.crisis = True # crisis is coming, no more money!
    >>> print(corner_street_atm.next())
    <type 'exceptions.StopIteration'>
    >>> wall_street_atm = hsbc.create_atm() # it's even true for new ATMs
    >>> print(wall_street_atm.next())
    <type 'exceptions.StopIteration'>
    >>> hsbc.crisis = False # trouble is, even post-crisis the ATM remains empty
    >>> print(corner_street_atm.next())
    <type 'exceptions.StopIteration'>
    >>> brand_new_atm = hsbc.create_atm() # build a new one to get back in business
    >>> for cash in brand_new_atm:
    ...    print cash
    $100
    $100
    $100
    $100
    $100
    $100
    $100
    $100
    $100
    ...
It can be useful for various things like controlling access to a resource.

###4.2 Itertools，你最好的朋友

iteratools模块包含了很多特殊的函数用来处理可迭代的对象。想要复制一个generator，链接两个generator，在一行代码内给一个嵌套list进行分组，不通过额外list来Map/Zip。你只需要`import itertools`

举个例子吧。让我们来给你个4匹马赛跑的可能排序吧：

    >>> horses = [1, 2, 3, 4]
    >>> races = itertools.permutations(horses)
    >>> print(races)
    <itertools.permutations object at 0xb754f1dc>
    >>> print(list(itertools.permutations(horses)))
    [(1, 2, 3, 4),
     (1, 2, 4, 3),
     (1, 3, 2, 4),
     (1, 3, 4, 2),
     (1, 4, 2, 3),
     (1, 4, 3, 2),
     (2, 1, 3, 4),
     (2, 1, 4, 3),
     (2, 3, 1, 4),
     (2, 3, 4, 1),
     (2, 4, 1, 3),
     (2, 4, 3, 1),
     (3, 1, 2, 4),
     (3, 1, 4, 2),
     (3, 2, 1, 4),
     (3, 2, 4, 1),
     (3, 4, 1, 2),
     (3, 4, 2, 1),
     (4, 1, 2, 3),
     (4, 1, 3, 2),
     (4, 2, 1, 3),
     (4, 2, 3, 1),
     (4, 3, 1, 2),
     (4, 3, 2, 1)]

###4.3 理解迭代的内部机制

迭代是一个处理可迭代对象和迭代器的过程。可迭代对象是一个可以返回迭代器的对象。迭代器是一个让你迭代一个可迭代对象的对象。
