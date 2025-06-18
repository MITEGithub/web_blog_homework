<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <div class="navbar">
    <div class="navbar-left">
      <img src="@/assets/logo.png" class="logo" />
      <span class="title">MITE_z</span>

      <el-button type="text" link @click="router.push('/')" class="nav-btn">
        <el-icon><HomeFilled /></el-icon> 首页
      </el-button>
      <el-button type="text" link @click="write" class="nav-btn">
        <el-icon><Edit /></el-icon> 写文章
      </el-button>
      <el-button type="text" link @click="home" class="nav-btn">
        <el-icon><Notebook /></el-icon> 我的文章
      </el-button>
    </div>

    <div class="navbar-right">
      <span class="username">👤 {{ userInfo.username }}</span>
      <el-button type="text" link @click="logout" class="nav-btn">
        <el-icon><SwitchButton /></el-icon> 注销
      </el-button>
    </div>
  </div>

  <div class="view-page">
    <el-card class="article-card">
      <h1>{{ article.title }}</h1>
      <div class="meta">👤 {{ article.author }} ｜ 🕒 {{ article.created_at }}</div>

      <v-md-preview :text="article.content" class="content-preview" />

      <div class="interaction-bar">
        <div class="interaction-left">
          <el-button round @click="handleLike" class="like-button">
            <el-icon><HeartFilled /></el-icon> {{ article.likes }} 点赞
          </el-button>
          <el-button round class="view-button" disabled>
            <el-icon><View /></el-icon> {{ article.views }} 浏览
          </el-button>
        </div>
        <div class="interaction-right">
          <el-button round class="share-button">
            <el-icon><Share /></el-icon> 分享
          </el-button>
        </div>
      </div>
    </el-card>

    <el-card class="comment-card">
      <template #header>
        <span class="comment-header">💬 评论 ({{ comments.length }})</span>
      </template>

      <!-- 评论列表 -->
      <div v-if="comments.length" class="comment-list">
        <div v-for="(c, index) in comments" :key="index" class="comment-item">
          <p class="comment-meta">
            <strong>{{ c.username }}</strong>
            <span class="time">{{ formatDate(c.created_at) }}</span>
          </p>
          <p class="comment-content">{{ c.content }}</p>
        </div>
      </div>
      <div v-else class="no-comment">暂无评论，快来抢沙发！</div>

      <!-- 评论输入框 -->
      <div class="comment-box">
        <el-input
          type="textarea"
          v-model="newComment"
          :autosize="{ minRows: 3 }"
          maxlength="500"
          show-word-limit
          placeholder="写下你的看法..."
        />
        <div class="comment-submit">
          <el-button type="primary" size="small" @click="submitComment">发表</el-button>
        </div>
      </div>
    </el-card>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { ElMessage } from 'element-plus'
import {
  HeartFilled, View, Share, SwitchButton,
  HomeFilled, Edit, Notebook
} from '@element-plus/icons-vue'

const route = useRoute()
const router = useRouter()
const token = localStorage.getItem('token')

const article = ref({
  title: '',
  author: '',
  created_at: '',
  views: 0,
  likes: 0,
  content: ''
})

const id = route.params.id
const comments = ref([])
const newComment = ref('')
const userInfo = ref({ username: '' })

const fetchArticle = async () => {
  const { data } = await axios.get(`/backend/api/views/content.php?id=${id}`)
  if (data.code === 0) {
    article.value = data.data
  }
}

const fetchComments = async () => {
  const { data } = await axios.get(`/backend/api/views/list.php?article_id=${id}`)
  if (data.code === 0) {
    comments.value = data.data
  }
}

const handleLike = async () => {
  const articleid = route.params.id
  const { data } = await axios.post('/backend/api/articles/like.php', { id: articleid }, {
    headers: { 'Content-Type': 'application/json' }
  })

  if (data.code === 0) {
    article.value.likes++
    ElMessage.success('点赞成功')
  } else {
    ElMessage.error(data.error || '点赞失败')
  }
}

const submitComment = async () => {
  if (!token) {
    ElMessage.info('请先登录后发表评论')
    router.push('/login')
    return
  }

  if (!newComment.value.trim()) {
    ElMessage.warning('评论内容不能为空')
    return
  }

  const { data } = await axios.post('/backend/api/articles/comment.php', {
    article_id: id,
    content: newComment.value,
    token
  }, {
    headers: { 'Content-Type': 'application/json' }
  })

  if (data.code === 0) {
    ElMessage.success('评论成功')
    newComment.value = ''
    fetchComments()
  } else {
    ElMessage.error(data.error || '评论失败')
  }
}

const formatDate = (datestr) => new Date(datestr).toLocaleString()

onMounted(async () => {
  await axios.post('/backend/api/articles/view_add.php', { id }, {
    headers: { 'Content-Type': 'application/json' }
  })

  fetchArticle()
  fetchComments()

  if (token) {
    const { data } = await axios.get('/backend/api/user_info/get.php', {
      headers: { Authorization: token }
    })
    if (data.code === 0) {
      userInfo.value = data.data
    }
  }
})

const logout = () => {
  localStorage.removeItem('token')
  router.replace('/login')
}

const write = () => {
  if (!token) {
    alert('请先登录')
    router.push('/login')
    return
  }
  router.push('/write')
}

const home = () => {
  if (!token) {
    alert('请先登录')
    router.push('/login')
    return
  }
  router.push('/home')
}
</script>

<style scoped>
.view-page {
  max-width: 900px;
  margin: 20px auto;
  padding: 10px;
}
.article-card {
  margin-bottom: 20px;
}
.meta {
  font-size: 13px;
  color: #888;
  margin-bottom: 10px;
}
.interaction-bar {
  display: flex;
  justify-content: space-between;
  margin-top: 10px;
}
.interaction-left .el-button,
.interaction-right .el-button {
  margin-right: 10px;
  border-radius: 20px;
}
.comment-card {
  margin-top: 20px;
}
.comment-header {
  font-weight: bold;
  font-size: 16px;
}
.comment-list {
  margin-top: 10px;
}
.comment-item {
  padding: 12px 0;
  border-top: 1px solid #eee;
}
.comment-item:first-child {
  border-top: none;
}
.comment-meta {
  font-size: 14px;
  color: #555;
  margin-bottom: 4px;
}
.comment-content {
  font-size: 14px;
  color: #333;
  line-height: 1.6;
  white-space: pre-wrap;
}
.time {
  font-size: 12px;
  color: #999;
  margin-left: 10px;
}
.comment-box {
  margin-top: 20px;
}
.comment-submit {
  margin-top: 10px;
  text-align: right;
}
.no-comment {
  padding: 12px 0;
  color: #999;
  font-style: italic;
}

/* 顶部导航栏样式 */
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 60px;
  background-color: #1976d2;
  padding: 0 20px;
  color: white;
  margin-bottom: 20px;
  border-radius: 4px;
}
.navbar-left,
.navbar-right {
  display: flex;
  align-items: center;
  gap: 15px;
}
.logo {
  width: 32px;
  height: 32px;
}
.title {
  font-size: 20px;
  font-weight: bold;
  margin-left: 10px;
  color: white;
}
.nav-btn {
  color: white;
  font-size: 14px;
}
.username {
  font-size: 14px;
  color: #fff;
}

.content-preview {
  margin-top: 20px;
  word-wrap: break-word;
  overflow-wrap: break-word;
  white-space: normal;
}
  .nav-btn {
  font-size: 15px;
  color: white !important; /* 强制白色字体 */
  padding: 4px 10px;
  border-radius: 6px;
}

.nav-btn .el-icon {
  color: white !important;  /* 图标也设为白色 */
  fill: white !important;
}
/* 保证按钮本身和里面的内容都白色显示 */
.nav-btn {
  font-size: 15px;
  color: white !important;
  padding: 4px 10px;
  border-radius: 6px;
}

/* 强化图标颜色（针对 el-icon 和 svg） */
.nav-btn .el-icon,
.nav-btn .el-icon svg {
  color: white !important;
  fill: white !important;
}

</style>
