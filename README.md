# blog

## 如果发现package没有安装，请执行以下命令
```
npm install --legacy-peer-deps (会根据package.json 文件中的具体包名下载对应文件)
```

## 前端启动
npm run lint -- --fix && npm run serve (规范格式， 再启动)

## 后端启动
php -S localhost:8000

## blog目录下有一下文件：
1. 项目说明文档.pdf
2. blog 文件夹 （源代码）
3. blog.sql （数据库文件）

### 数据库部署指令
```
mysql -u root -p blog < blog.sql

或者你也可以之下使用下面的指令创建：

user:
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  token VARCHAR(64) UNIQUE,
  role ENUM('user', 'admin') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

articles:
CREATE TABLE articles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  summary TEXT,
  content TEXT,
  author_id INT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  views INT DEFAULT 0,
  likes INT DEFAULT 0,
  FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

comments:
CREATE TABLE comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  article_id INT NOT NULL,
  user_id INT NOT NULL,
  content TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

```
