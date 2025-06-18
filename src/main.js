import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'

import VMdEditor from '@kangc/v-md-editor'
import '@kangc/v-md-editor/lib/style/base-editor.css'

import VMdPreview from '@kangc/v-md-editor/lib/preview'
import '@kangc/v-md-editor/lib/style/preview.css'

// 正确的主题导入在代码中写，不是 npm install 的目标！
import vuepressTheme from '@kangc/v-md-editor/lib/theme/vuepress'
import '@kangc/v-md-editor/lib/theme/style/vuepress.css'

import Prism from 'prismjs'

VMdEditor.use(vuepressTheme, { Prism })
VMdPreview.use(vuepressTheme, { Prism })

const app = createApp(App)
app.use(VMdEditor)

app.use(VMdPreview)

app.use(router)
app.use(ElementPlus)
app.mount('#app')
