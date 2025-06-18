<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <div class="login-container">
    <div class="login-box">
      <img class="avatar" src="@/assets/avatar.png" alt="avatar" />
      <h2 class="login-title">博客用户登录</h2>
      <p class="subtitle">代码改变世界</p>
      <a class="vip-link">分享你的代码创意吧!(•̤̀ᵕ•̤́๑)ᵒᵏᵎᵎᵎᵎ</a>

      <div class="tabs-wrapper">
        <el-tabs v-model="activeTab" stretch>
          <el-tab-pane label="用户名登录" name="username">
            <el-form :model="form.usernameLogin" class="login-form">
              <el-form-item>
                <el-input v-model="form.usernameLogin.username" placeholder="用户名" />
              </el-form-item>
              <el-form-item>
                <el-input
                  v-model="form.usernameLogin.password"
                  type="password"
                  show-password
                  placeholder="密码"
                />
              </el-form-item>
              <div class="form-options">
                <el-checkbox v-model="rememberMe">记住我</el-checkbox>
                <div class="forgot-links">
                  <a>忘记用户名</a>
                  <a>忘记密码</a>
                </div>
              </div>
              <el-button type="primary" class="login-btn" @click="handleUsernameLogin">登录</el-button>
            </el-form>
          </el-tab-pane>

          <el-tab-pane label="邮箱登录" name="email">
            <el-form :model="form.emailLogin" class="login-form">
              <el-form-item>
                <el-input v-model="form.emailLogin.email" placeholder="邮箱" />
              </el-form-item>
              <el-form-item>
                <el-input
                  v-model="form.emailLogin.password"
                  type="password"
                  show-password
                  placeholder="密码"
                />
              </el-form-item>
              <el-button type="primary" class="login-btn" @click="handleEmailLogin">登录</el-button>
            </el-form>
          </el-tab-pane>
        </el-tabs>
      </div>

      <div class="divider">第三方登录/注册</div>
      <div class="social-icons">
        <img src="@/assets/wechat.png" alt="微信" />
        <img src="@/assets/qq.png" alt="QQ" />
        <img src="@/assets/github.png" alt="GitHub" />
      </div>

      <div class="register-link">
        没有账号？<router-link to="/register">立即注册</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const activeTab = ref('username')
const rememberMe = ref(false)

const form = reactive({
  usernameLogin: {
    username: '',
    password: ''
  },
  emailLogin: {
    email: '',
    password: ''
  }
})

const handleUsernameLogin = async () => {
  const { username, password } = form.usernameLogin
  if (!username || !password) return alert('请填写完整信息')
  try {
    const res = await axios.post('/backend/api/login.php', { username, password })
    if (res.data.token) {
      localStorage.setItem('token', res.data.token)
      alert('登录成功')
      router.push('/home')
    } else {
      alert(res.data.error || '登录失败')
    }
  } catch {
    alert('服务器错误')
  }
}

const handleEmailLogin = async () => {
  const { email, password } = form.emailLogin
  if (!email || !password) return alert('请填写完整信息')
  try {
    const res = await axios.post('/backend/api/login.php', { email, password })
    if (res.data.token) {
      localStorage.setItem('token', res.data.token)
      alert('登录成功')
      router.push('/home')
    } else {
      alert(res.data.error || '登录失败')
    }
  } catch {
    alert('服务器错误')
  }
}
</script>

<style scoped>
.login-container {
  background: #e9ecf1;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}
.login-box {
  background: #fff;
  padding: 30px;
  width: 360px;
  border-radius: 12px;
  text-align: center;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
}
.avatar {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  margin-bottom: 10px;
}
.login-title {
  font-size: 20px;
  margin-bottom: 6px;
}
.subtitle {
  font-size: 13px;
  color: #888;
  margin-bottom: 2px;
}
.vip-link {
  font-size: 13px;
  color: #409eff;
  margin-bottom: 12px;
  display: block;
}
.tabs-wrapper {
  text-align: center;
}
.login-form {
  margin-top: 10px;
  text-align: left;
}
.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
  margin-bottom: 12px;
}
.forgot-links a {
  margin-left: 10px;
  color: #409eff;
  cursor: pointer;
}
.login-btn {
  width: 100%;
  margin-bottom: 10px;
}
.divider {
  margin: 14px 0 6px;
  font-size: 13px;
  color: #aaa;
}
.social-icons {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin-bottom: 10px;
}
.social-icons img {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  cursor: pointer;
  border: 1px solid #ddd;
  padding: 3px;
  background: #fff;
}
.register-link {
  margin-top: 5px;
  font-size: 14px;
}
.register-link a {
  color: #409eff;
  margin-left: 5px;
}
</style>
