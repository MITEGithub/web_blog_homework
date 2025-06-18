<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <!-- 顶部导航栏 -->
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

  <div class="home-container">
    <!-- 左侧用户信息 -->
    <div class="left-panel">
      <el-card class="user-card">
        <div class="user-content">
          <img src="@/assets/avatar.png" class="avatar" />
          <h3 class="user-name">{{ userInfo.username }}</h3>
          <p class="email">{{ userInfo.email }}</p>
          <p class="joined">加入时间：{{ userInfo.joined }}</p>
          <el-button v-if="userInfo.role === 'admin'" type="danger" size="small" plain>管理员</el-button>
          <el-button class="mt-2" type="primary" plain size="small">编辑资料</el-button>
          <el-button class="mt-3" type="primary" @click="goWrite">✚ 写新文章</el-button>
        </div>
      </el-card>

      <el-card class="stats-card" shadow="hover">
        <template #header>📊 我的统计</template>
        <div class="stats-grid">
          <div v-for="item in statsList" :key="item.label" class="stat-item">
            <div :style="{ color: item.color }">{{ item.value }}</div>
            <small>{{ item.label }}</small>
          </div>
        </div>
      </el-card>
    </div>

    <!-- 右侧文章列表 -->
    <div class="right-panel">
      <el-card>
        <template #header>
          <div class="header-row">
            <span>📚 我的文章</span>
          </div>
        </template>

        <el-table :data="filteredArticles" border>
          <el-table-column prop="title" label="标题" min-width="200" />
          <el-table-column label="数据" min-width="120">
            <template #default="{ row }">
              👁️ {{ row.views }} 💬 {{ row.comment_count }} ❤️ {{ row.likes }}
            </template>
          </el-table-column>
          <el-table-column prop="created_at" label="创建时间" min-width="150" />
           <el-table-column label="操作" width="180">
             <template #default="scope">
               <el-button type="primary" circle plain size="small" @click="editArticle(scope.row.id)">
                 <el-icon><Edit /></el-icon>
               </el-button>
               <el-button type="success" circle plain size="small" @click="viewArticle(scope.row.id)">
                 <el-icon><View /></el-icon>
               </el-button>
               <el-button type="danger" circle plain size="small" @click="deleteArticle(scope.row.id)">
                 <el-icon><Delete /></el-icon>
               </el-button>
             </template>
           </el-table-column>

        </el-table>
      </el-card>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

import { HomeFilled, Edit, View, Delete, Notebook, SwitchButton } from '@element-plus/icons-vue'

import { ElMessage } from 'element-plus'

const router = useRouter()
const token = localStorage.getItem('token')

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

// 用户信息
const userInfo = ref({
  username: '',
  email: '',
  joined: '',
  role: 'user'
})

// 文章统计数据
const stats = ref({
  total: 0,
  published: 0,
  drafts: 0,
  views: 0,
  likes: 0,
  comments: 0
})

const articles = ref([])
const search = ref('')

// 文章过滤搜索
const filteredArticles = computed(() => {
  if (!search.value) return articles.value
  return articles.value.filter(a => a.title.includes(search.value))
})

// 显示统计配色
const statsList = computed(() => [
  { label: '总文章', value: stats.value.total, color: '#409EFF' },
  { label: '已发布', value: stats.value.published, color: '#67C23A' },
  { label: '草稿', value: stats.value.drafts, color: '#E6A23C' },
  { label: '总浏览', value: stats.value.views, color: '#909399' },
  { label: '获赞', value: stats.value.likes, color: '#F56C6C' },
  { label: '评论', value: stats.value.comments, color: '#303133' }
])

// 获取用户信息
const fetchUserInfo = async () => {
  const { data } = await axios.get('/backend/api/user_info/get.php', {
    headers: { Authorization: token }
  })
  if (data.code === 0) {
    userInfo.value = {
      username: data.data.username,
      email: data.data.email,
      role: data.data.role,
      joined: data.data.created_at.slice(0, 7).replace('-', '年') + '月'
    }
  }
}

// 获取统计
const fetchStats = async () => {
  const { data } = await axios.get('/backend/api/user/my_stats.php', {
    headers: { Authorization: token }
  })
  if (data.code === 0) stats.value = data.data
}

// 获取文章列表
const fetchArticles = async () => {
  const { data } = await axios.get('/backend/api/user/my_articles.php', {
    headers: { Authorization: token }
  })
  if (data.code === 0) articles.value = data.data
}

const goWrite = () => {
  router.push('/write')
}

const editArticle = (id) => {
  router.push(`/edit/${id}`)
}

const viewArticle = (id) => {
  router.push(`/view/${id}`)
}

const deleteArticle = async (id) => {
  if (!confirm('确定删除此文章？')) return

  const { data } = await axios.post('/backend/api/articles/delete.php', { id }, {
    headers: { Authorization: token }
  })

  if (data.code === 0) {
    ElMessage.success('删除成功')
    fetchArticles()
  } else {
    ElMessage.error(data.msg || '删除失败')
  }
}

onMounted(() => {
  fetchUserInfo()
  fetchStats()
  fetchArticles()
})
</script>

<style scoped>
.home-container {
  display: flex;
  gap: 20px;
  padding: 20px;
}
.left-panel {
  width: 260px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.right-panel {
  flex: 1;
}
.user-card {
  display: flex;
  justify-content: center;
  padding: 20px 0;
}
.user-content {
  text-align: center;
}
.avatar {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  margin-bottom: 10px;
}
.username {
  font-size: 16px;
  margin-bottom: 4px;
  color: #333;
}
.email {
  font-size: 13px;
  color: #999;
  margin-bottom: 4px;
}
.joined {
  font-size: 13px;
  margin-bottom: 10px;
  color: #666;
}
.mt-2 {
  margin-top: 10px;
  margin-bottom: 25px;
  width: 130px;
}
.mt-3 {
  margin-top: 0px;
  width: 140px;
  position: absolute;
  left: 75px;
  top: 380px;
}
.stats-grid {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
}
.stat-item {
  width: 45%;
  text-align: center;
  margin-bottom: 10px;
}
.header-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
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
.user-name {
  font-size: 16px;
  color: #333;
  margin-bottom: 4px;
}

</style>
