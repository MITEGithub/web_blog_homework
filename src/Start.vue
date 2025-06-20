<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <transition name="fade-slide">
    <div class="home-page">
      <div v-if="showWelcome" class="welcome-banner">
        <img src="@/assets/logo.png" class="banner-logo" />
        <h2>📚 我的博客系统 <span class="sparkle">✨</span></h2>
        <p class="welcome-text">欢迎回来！你今天想读点什么？😊</p>
         <lottie-player
        src="https://assets2.lottiefiles.com/packages/lf20_HpFqiS.json"
        background="transparent"
        speed="1"
        style="width: 300px; height: 300px; margin: 0 auto"
        loop
        autoplay
      ></lottie-player>

        <el-button type="primary" size="large" @click="startApp" class="start-button">
          🚀 开始浏览
        </el-button>
      </div>

      <div v-else>
        <div class="navbar">
          <div class="navbar-left">)
            <img src="@/assets/logo.png" class="logo" />
            <span class="title">MITE_Blog</span>
            <el-button type="text" link @click="$router.push('/')" class="nav-btn">
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
            <el-button type="text" link @click="$router.push('/login')" class="nav-btn">
              <el-icon><User /></el-icon> 登录
            </el-button>
            <el-button type="text" link @click="$router.push('/register')" class="nav-btn">
              <el-icon><UserFilled /></el-icon> 注册
            </el-button>
          </div>
        </div>

        <div class="main-wrapper">
          <div class="center-panel">
            <h2 class="section-title">📌 最新文章</h2>
            <div class="article-grid">
              <div v-for="article in articles" :key="article.id" class="article-card-box">
                <el-card class="article-elcard" shadow="hover">
                  <h3 class="article-title" @click="$router.push(`/view/${article.id}`)">
                    📝 {{ article.title }}
                  </h3>

                  <p class="summary">💡 {{ truncate(article.summary, 100) }}</p>

                  <div class="meta-row left-align">
                    <span><el-icon><User /></el-icon> {{ article.author }}</span>
                    <span><el-icon><Clock /></el-icon> {{ formatDate(article.created_at) }}</span>
                    <span><el-icon><View /></el-icon> {{ article.views }}</span>
                  </div>

                  <div class="card-actions">
                    <div class="left-actions">
                      <el-button size="small" round plain @click="likeArticle(article.id)">
                        ❤️ {{ article.likes }}
                      </el-button>
                      <el-button size="small" round plain @click="$router.push(`/view/${article.id}`)">
                        💬 评论
                      </el-button>
                    </div>
                    <el-button size="small" type="primary" round plain @click="$router.push(`/view/${article.id}`)">
                      🔍 阅读更多
                    </el-button>
                  </div>
                </el-card>
              </div>

            </div>
          </div>

          <div class="right-panel">
            <div class="block-card">
              <div class="block-title">
                <el-icon><DataBoard /></el-icon> 网站统计
              </div>
              <div class="stat-group grid-layout">
                <div class="stat-item blue">
                  <el-icon><Document /></el-icon>
                  <div>
                    <p class="stat-num">{{ stats.articles }}</p>
                    <p class="stat-label">文章数</p>
                  </div>
                </div>
                <div class="stat-item green">
                  <el-icon><UserFilled /></el-icon>
                  <div>
                    <p class="stat-num">{{ stats.users }}</p>
                    <p class="stat-label">用户数</p>
                  </div>
                </div>
                <div class="stat-item orange">
                  <el-icon><ChatLineSquare /></el-icon>
                  <div>
                    <p class="stat-num">{{ stats.comments }}</p>
                    <p class="stat-label">评论数</p>
                  </div>
                </div>
                <div class="stat-item red">
                  <el-icon><Star /></el-icon>
                  <div>
                    <p class="stat-num">{{ stats.likes }}</p>
                    <p class="stat-label">点赞数</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="block-card">
              <div class="block-title">
                <el-icon><Star /></el-icon> 热门文章
              </div>
              <ul class="block-list">
                <li v-for="item in popular" :key="item.id">
                  <el-icon><View /></el-icon> {{ item.title }}（{{ parseInt(item.views) +parseInt(item.likes) }}）
                </li>
              </ul>
            </div>
            <div class="block-card">
              <div class="block-title">
                <el-icon><ChatDotRound /></el-icon> 最新评论
              </div>
              <ul class="block-list">
                <li v-for="c in comments" :key="c.id">
                  <b>{{ c.user }}</b>：{{ c.content }}
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div class="pagination-wrapper">
          <el-pagination
            background
            layout="prev, pager, next"
            :total="total"
            :page-size="20"
            :pager-count="7"
            @current-change="handlePageChange"
          />
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import {
  User, View, Star,
  ChatLineSquare, Document, UserFilled,
  ChatDotRound, DataBoard,
  HomeFilled, Edit, Notebook
} from '@element-plus/icons-vue'
import { useRouter } from 'vue-router'
import { ElMessage } from 'element-plus'

const showWelcome = ref(true)
const startApp = () => {
  showWelcome.value = false
}

const articles = ref([])
const total = ref(0)
const stats = ref({ articles: 0, users: 0, comments: 0, likes: 0 })
const popular = ref([])
const comments = ref([])

const router = useRouter()
const token = localStorage.getItem('token')

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

const fetchArticles = async (page = 1) => {
  const res = await axios.get(`/backend/api/global/articles.php?page=${page}`)
  console.log(res.data)
  articles.value = res.data
  total.value = 1000
}
const fetchStats = async () => {
  const res = await axios.get('/backend/api/global/stats.php')
  stats.value = res.data
}
const fetchPopular = async () => {
  const res = await axios.get('/backend/api/global/popular.php')
  popular.value = res.data
}
const fetchComments = async () => {
  const res = await axios.get('/backend/api/global/comments.php')
  comments.value = res.data
}
const handlePageChange = (page) => {
  fetchArticles(page)
}
const likeArticle = async (id) => {
  console.log('点赞文章', id)
  const { data } = await axios.post('/backend/api/articles/like.php', { id: id })
  console.log(data)
  if (data.code !== 0) {
    ElMessage.error(data.error || '点赞失败')
    return
  }

  ElMessage.success('点赞成功')
  fetchArticles()
}
const formatDate = (dateStr) => new Date(dateStr).toLocaleDateString()
const truncate = (str, len) => str.length > len ? str.slice(0, len) + '...' : str

onMounted(() => {
  fetchArticles()
  fetchStats()
  fetchPopular()
  fetchComments()
})
</script>

<style scoped>
@import 'animate.css';

.home-page {
  background: #f5f7fa;
  min-height: 100vh;
  padding-bottom: 70px;
}

.navbar {
  background: #0066ff;
  height: 52px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 30px;
  color: white;
}
.navbar-left, .navbar-right {
  display: flex;
  align-items: center;
  gap: 14px;
}
.logo {
  height: 26px;
}
.title {
  font-size: 20px;
  font-weight: bold;
  color: white;
}
.nav-btn {
  font-size: 15px;
  color: white;
  padding: 4px 10px;
  border-radius: 6px;
}
.nav-btn:hover {
  background-color: rgba(255, 255, 255, 0.15);
  color: #cce6ff;
}

.main-wrapper {
  max-width: 1300px;
  margin: 0 auto;
  padding: 0 24px;
  display: flex;
  gap: 24px;
}

.center-panel {
  flex: 1;
}
.right-panel {
  width: 260px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.section-title {
  font-size: 24px;
  font-weight: bold;
  color: #007fff;
  border-bottom: 2px solid #007fff;
  display: inline-block;
  padding-bottom: 4px;
  margin-bottom: 16px;
}
.article-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
}
.article-card {
  width: calc(50% - 10px);
}
.article-elcard {
  border-radius: 14px;
  padding: 16px;
}
.article-title {
  font-size: 18px;
  font-weight: bold;
  margin-bottom: 6px;
}
.meta {
  font-size: 13px;
  color: #999;
  margin-bottom: 8px;
}
.summary {
  font-size: 14px;
  color: #333;
  margin-bottom: 12px;
  line-height: 1.5;
}
.card-actions {
  display: flex;
  justify-content: space-between;
  gap: 8px;
}
.block-card {
  background: white;
  border-radius: 14px;
  padding: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  max-width: 500px;
  width: 100%;
}
.block-title {
  font-size: 16px;
  font-weight: bold;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.block-list {
  list-style: none;
  padding-left: 0;
  font-size: 14px;
}
.block-list li {
  margin-bottom: 8px;
}
.grid-layout {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}
.stat-item {
  display: flex;
  align-items: center;
  gap: 20px; /* 增加图标与文字之间的距离 */
  color: white;
  padding: 15px;
  border-radius: 12px;
}

/* 图标大小设置 */
.stat-item .el-icon {
  font-size: 30px; /* 放大图标 */
  flex-shrink: 0;  /* 防止图标被压缩 */
}

.stat-num {
  font-size: 20px;
  font-weight: bold;
  margin: 0;
  line-height: 1;
}

.stat-label {
  font-size: 14px;
  margin-top: 4px;
  opacity: 0.95;
}

.blue { background: #409eff; }
.green { background: #67c23a; }
.orange { background: #e6a23c; }
.red { background: #f56c6c; }

.pagination-wrapper {
  position: fixed;
  bottom: 16px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 10;
  background: #f5f7fa;
  padding: 8px 16px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}
.start-button {
  margin-top: 20px;
}
.welcome-banner {
  text-align: center;
  padding: 40px 20px;
  background: #f8f9fc;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  animation: fadeIn 1s;
}
.banner-logo {
  width: 100px;
  height: 100px;
  margin-bottom: 10px;
  border-radius: 24px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.welcome-text {
  font-size: 1.2em;
  margin: 8px 0 20px;
  color: #444;
}
.welcome-illustration {
  max-width: 400px;
  margin: 30px auto 0;
}
.start-button {
  margin-top: 30px;
  font-size: 16px;
}
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: opacity 0.5s, transform 0.5s;
}
.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(10px);
}
.article-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(420px, 1fr));
  gap: 20px;
}

.article-card-box {
  border-radius: 16px;
}

.article-title {
  font-size: 18px;
  font-weight: bold;
  margin-bottom: 8px;
}

.summary {
  font-size: 14px;
  color: #333;
  margin-bottom: 12px;
}

.meta-row {
  font-size: 13px;
  color: #888;
  display: flex;
  justify-content: space-between;
  margin-bottom: 14px;
}

.card-actions {
  display: flex;
  justify-content: space-between;
  gap: 10px;
}
.meta-row.left-align {
  justify-content: flex-start;
  gap: 20px;
}

.card-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.left-actions {
  display: flex;
  gap: 12px;
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

</style>
