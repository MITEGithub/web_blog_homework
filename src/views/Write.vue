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

  <div class="edit-container">
    <div class="title-bar">
      <span class="page-title">✏️ 写文章</span>
    </div>

    <el-card class="edit-card">
      <el-form label-position="top">
        <el-form-item label="文章标题" required>
          <el-input
            v-model="title"
            maxlength="255"
            show-word-limit
            placeholder="请输入文章标题"
          />
        </el-form-item>

        <el-form-item label="文章摘要">
          <el-input
            type="textarea"
            v-model="summary"
            maxlength="500"
            show-word-limit
            :autosize="{ minRows: 3, maxRows: 6 }"
            placeholder="简要描述文章内容（如为空将自动生成摘要）"
          />
        </el-form-item>

        <el-form-item label="文章内容" required>
          <v-md-editor v-model="content" height="400px" />
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="handleSubmit">发布文章</el-button>
          <el-button @click="router.push('/home')">取消</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { ElMessage } from 'element-plus'
import {
  HomeFilled,
  Edit,
  Notebook,
  SwitchButton
} from '@element-plus/icons-vue'

const router = useRouter()
const token = localStorage.getItem('token')

const title = ref('')
const summary = ref('')
const content = ref('')

const userInfo = ref({ username: '' })

onMounted(async () => {
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

const handleSubmit = async () => {
  if (!title.value.trim() || !content.value.trim()) {
    ElMessage.warning('标题和内容不能为空')
    return
  }

  const res = await axios.post('/backend/api/articles/publish.php', {
    title: title.value,
    summary: summary.value,
    content: content.value,
    token
  })

  if (res.data.message === '发布成功') {
    ElMessage.success('文章发布成功')
    router.push('/home')
  } else {
    ElMessage.error(res.data.error || '发布失败')
  }
}

</script>

<style scoped>
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

.edit-container {
  padding: 30px;
  background-color: #f5f6fa;
  min-height: calc(100vh - 60px);
}
.title-bar {
  font-size: 20px;
  font-weight: bold;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 8px;
  color: #333;
}
.edit-card {
  max-width: 900px;
  margin: auto;
  background: white;
  border-radius: 10px;
  padding: 30px 40px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}
.el-form-item:last-of-type {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}
.el-button {
  min-width: 90px;
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
.nav-btn {
  font-size: 15px;
  color: white !important;
  padding: 4px 10px;
  border-radius: 6px;
}

.nav-btn .el-icon,
.nav-btn .el-icon svg {
  color: white !important;
  fill: white !important;
}

</style>
