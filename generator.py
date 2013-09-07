# code=utf8

from jinja2 import Environment, FileSystemLoader
# import post
import markdown
import os


class Post:
  def __init__(self, title, content, created_at):
    self.title = title
    self.content = content
    self.created_at = created_at
    self.url = created_at + "-" + title + ".html"

# posts
posts = []
files = os.listdir("./posts/")
for file_name in files:
  file_name = file_name.decode("utf-8")
  created_at = file_name[:10]
  title = file_name[11:-3]
  content = ""
  with open("./posts/"+file_name) as f:
    content = f.read().decode("utf-8")
  content = markdown.markdown(content)
  post = Post(title, content, created_at)
  posts.append(post)

# sort by date
posts = sorted(posts, key=lambda x:x.created_at)

# render index


env = Environment(loader=FileSystemLoader('./layouts/', encoding='utf-8'))
template = env.get_template('index.html')
html_index = template.render(posts=posts)
with open("index.html", "w") as f:
  f.write(html_index.encode("utf-8"))

# render posts
template = env.get_template("show.html")
for post in posts:
  html_show = template.render(post=post)
  with open("sites/"+post.url, "w") as f:
    f.write(html_show.encode("utf-8"))