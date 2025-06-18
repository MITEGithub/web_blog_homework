<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <div class="register-container">
    <div class="register-box">
      <h2 class="register-title">博客用户注册</h2>

      <el-form :model="form" class="register-form">
        <el-form-item>
          <el-input v-model="form.email" placeholder="邮箱" />
        </el-form-item>
        <el-form-item>
          <el-input v-model="form.username" placeholder="昵称" />
        </el-form-item>
        <el-form-item>
          <el-input v-model="form.password" type="password" show-password placeholder="密码" />
        </el-form-item>
        <el-form-item>
          <el-input
            v-model="form.confirm"
            type="password"
            show-password
            placeholder="确认密码"
          />
        </el-form-item>
        <el-button type="primary" class="register-btn" @click="handleRegister">注册</el-button>
      </el-form>

      <div class="terms">
        注册即表示您同意并愿意遵守
        <a>用户协议</a> 与 <a>隐私政策</a>
      </div>

      <div class="divider">第三方登录/注册</div>
      <div class="social-icons">
        <img src="@/assets/wechat.png" alt="微信" />
        <img src="@/assets/qq.png" alt="QQ" />
        <img src="@/assets/github.png" alt="GitHub" />
      </div>

      <div class="login-link">
        已有账号？<router-link to="/login">立即登录</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const form = reactive({
  username: '',
  email: '',
  password: '',
  confirm: ''
})

const handleRegister = async () => {
  if (!form.username || !form.email || !form.password || !form.confirm) {
    alert('请填写完整信息')
    return
  }

  if (form.password !== form.confirm) {
    alert('两次密码不一致')
    return
  }

  try {
    const res = await axios.post('/backend/api/register.php', {
      username: form.username,
      email: form.email,
      password: form.password
    })

    if (res.data.token) {
      alert('注册成功，请登录')
      router.push('/login')
    } else {
      alert(res.data.error || '注册失败')
    }
  } catch (err) {
    alert('服务器错误')
  }
}
</script>

<style scoped>
.register-container {
  background: #e9ecf1;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

.register-box {
  background: #fff;
  padding: 28px;
  width: 320px;
  border-radius: 12px;
  text-align: center;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
}

.register-title {
  font-size: 22px;
  margin-bottom: 20px;
}

.register-form {
  text-align: left;
}

.register-btn {
  width: 100%;
  margin-top: 10px;
}

.terms {
  font-size: 12px;
  color: #888;
  margin-top: 10px;
}
.terms a {
  color: #409eff;
  margin: 0 2px;
  cursor: pointer;
}

.divider {
  margin: 16px 0 6px;
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

.login-link {
  margin-top: 10px;
  font-size: 14px;
}
.login-link a {
  color: #409eff;
  margin-left: 5px;
}
</style>
