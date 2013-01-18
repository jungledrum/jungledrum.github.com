声明：以下问题皆是本人所遇，解决方法也是网上搜集整理，并不一定完全适用你的问题，更不对你执行操作（尤其是Ubuntu的安装）造成的数据丢失等问题负责:)
***
Ubuntu
------------
ubuntu镜像版本为11.10-amd64，随后升级至12.04    

* 安装  
从win7下硬盘安装，使用软件easybcd,menu.lst文件内容如下：  

        title Install Ubuntu
        root  (hd0,0)       
        kernel /vmlinuz boot=casper iso-scan/filename=/ubuntu-9.10-desktop-i386.iso   ro quiet splash locale=zh_CN.UTF-8  
        initrd /initrd.lz  
     
* 网速问题  
原因不明。目前解决办法为重装网卡驱动，去官网下载驱动后执行setup.sh脚本进行安装。DNS解析我用的是Google的8.8.8.8和8.8.4.4

* Chrome无法安装，deb包结构有问题  
先装chrouim，再`sudo apt-get install lzma lzma-dev`(貌似是一个7zip解压程序)

* sudo每次都要输入密码  
将用户加入sudo组，`sudo visudo`，具体怎么编辑请自行搜索

* 修改字体  
安装gnome-tweak-tool
***
Rails
------------
* 安装  
请参考这个[链接](http://ruby-china.org/wiki/install_ruby_guide)

* RVM is not a function 错误解决方法  
在你的~/.bashrc文件里面加入下面的代码  

        [[ -s "$HOME/.rvm/scripts/rvm" ]] && . "$HOME/.rvm/scripts/rvm"  
再执行 

        source ~/.bashrc    

* Could not find a JavaScript runtime execjs错误  
 
        sudo add-apt-repository ppa:chris-lea/node.js  
        sudo apt-get update  
        sudo apt-get install nodejs  

* MySQL的安装   
新立得软件包有，Ubuntu11.04是MySQL5.1，Ubuntu12.04是MySQL5.5

* MySQL GUI的选择  
Emma  
乱码问题可参考[这篇](http://blog.csdn.net/apoxlo/article/details/6967085)解决  
MySQL Workbench  
如果你Ubuntu升级到了12.04，MySQL官网下的很可能无法用，可以到[这里](https://launchpad.net/~olivier-berten/+archive/misc)下载

* 开发工具的选择  
IDE可选择RubyMine  
编辑器可选择Sublime    
ps:Vi/Emacs还在学习中:)
***
