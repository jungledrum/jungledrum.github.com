# coding=utf-8

require "erb"
require "./post.rb"
require "maruku"

@posts = Array.new
Dir.foreach("posts") do |x|
  unless x == "." or x == ".."
    title = x.match('(?<=.{11})(.*)(?=\..*)')[0]
    created_date = x[0,10]
    content = File.new("posts/#{x}").read
    doc = Maruku.new(content)
    content = doc.to_html

    post = Post.new(title, content, created_date)
    @posts << post
  end
end

@posts.sort! do |x,y|
  -(x.created_date <=> y.created_date)
end

f = File.new("layouts/index.html")
html = ERB.new(f.read).result(binding)

File.open("index.html", "w") do |x|
  x.puts(html)
end

@posts.each do |post|
  template = File.new("layouts/show.html")
  html = ERB.new(template.read).result(binding)
  File.open("sites/#{post.url}", "w") do |x|
    x.puts(html)
  end
end
