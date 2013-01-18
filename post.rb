class Post
  attr_accessor :title, :content, :url, :created_date, :updated_date,
                :filename

  def initialize(title, content, created_date)
    @title = title
    @content = content
    @created_date = created_date
    @url = "#{created_date}-#{title}.html"
  end
end
