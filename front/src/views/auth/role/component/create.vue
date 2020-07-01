<template>
  <el-dialog
    :title="title"
    center
    width="70%"
    :visible.sync="dialogVisible"
    :fullscreen="fullscreen"
  >
    <el-form ref="dataForm" :rules="rules" :model="defaultForm" label-position="left" label-width="70px" style="width: 400px; margin-left:50px;">
      <el-form-item label="名称" prop="name" placeholder="角色名">
        <el-input v-model="defaultForm.name" />
      </el-form-item>

      <el-form-item label="备注" prop="remark">
        <el-input v-model="defaultForm.remark" :autosize="{ minRows: 2, maxRows: 4}" type="textarea" placeholder="角色备注" />
      </el-form-item>

      <el-form-item label="权限" prop="menus">
        <el-tree
          ref="tree"
          :check-strictly="checkStrictly"
          :data="routesData"
          :props="defaultProps"
          show-checkbox
          node-key="path"
          class="permission-tree"
        />
      </el-form-item>

    </el-form>

    <div slot="footer" class="dialog-footer">
      <el-button @click="dialogVisible = false">取 消</el-button>
      <el-button @click="handleCreate">提 交</el-button>
    </div>

  </el-dialog>
</template>

<script>
  import { storeOrUpdate, getOne , getAllMenus , getThisRoleMenus } from '@/api/auth/role_api'
  import { isApiSuccess } from '@/configs/apicode'
  import path from "path";
  import { deepClone } from '@/utils'

  export default {
    name: 'RoleCreate',
    props: {
      createVisible: {
        type: Boolean,
        default: false
      },
      editOrCreate: {
        type: String,
        default: 'create'
      },
      editRow: {
        type: Object,
        default: () => {}
      }
    },
    computed: {
      routesData() {
        return this.routes
      }
    },
    data() {
      return {
        loading: false,
        title: '',
        dialogVisible: this.createVisible,
        fullscreen: false,
        defaultForm: {
          id: '',
          name: '',
          remark: '',
          menus: '',
        },
        rules: {
          name: [{ required: true, message: '名称必填', trigger: 'blur' }],
          remark: [{ required: true, message: '备注必填', trigger: 'blur' }],
        },
        checkStrictly: false,
        defaultProps: {
          children: 'children',
          label: 'title'
        },
        routes: [],
        serviceRoutes: [],
        thisRoleMenus: [],
      }
    },
    watch: {
      dialogVisible(newValue, oldValue) {
        this.$emit('update:createVisible', newValue)
      },
      createVisible(newValue, oldValue) {
        this.dialogVisible = newValue
      }
    },
    created() {
      if (this.$store.state.app.device === 'mobile') {
        this.fullscreen = true
      }

      this.getRoutes()

      if (this.editOrCreate === 'create'){
        this.title = '角色新增'
        this.$nextTick(() => {
          this.$refs.tree.setCheckedNodes([])
        })
      } else {
        this.title = '角色编辑'
        this.getFormData(this.editRow.id)
        this.$nextTick(() => {
          this.setCheckedRoutes()
        })
      }
    },
    methods: {
      async getRoutes() {
        const res = await getAllMenus()
        this.serviceRoutes = res.data.data
        this.routes = this.generateRoutes(this.serviceRoutes)
      },
      getCheckedRoutes() {
         const ajaxData = {id: this.editRow.id}
        getThisRoleMenus(ajaxData).then(response => {
          if (isApiSuccess(response.code)) {
            this.thisRoleMenus = response.data.data
          }
        })
      },
      // 执行创建
      handleCreate() {
        const checkedKeys = this.$refs.tree.getCheckedKeys()
        console.log(checkedKeys)
        this.defaultForm.menus = checkedKeys
        this.$refs['createForm'].validate(valid => {
          if (valid) {
            storeOrUpdate(this.defaultForm).then(response => {
              if (isApiSuccess(response.code)) {
                this.$message({
                  message: 'ok',
                  type: 'success',
                  duration: 1500
                })
                this.createVisible = false
                this.$emit('getList')
              } else {
                this.$message({
                  message: response.msg || 'error',
                  type: 'error',
                  duration: 3000
                })
              }
            })
          } else {
            return false
          }
        })
      },
      getFormData(id) {
        const ajaxData = { id: id }
        getOne(ajaxData).then(response => {
          console.log(response)
          if (isApiSuccess(response.code)) {
            this.defaultForm = {
              id: response.data.data.id,
              name: response.data.data.name,
              phone: response.data.data.phone,
              contact_user: response.data.data.contact_user,
              admin_status: response.data.data.admin_status.toString(),
              remark: response.data.data.remark
            }
          } else {
            this.$message({
              message: response.msg || 'error',
              type: 'error',
              duration: 3000
            })
          }
        })
      },
      generateRoutes(routes, basePath = '/') {
        const res = []

        for (let route of routes) {
          route.path = route.url

          // skip some route
          if (route.hidden) { continue }

          const onlyOneShowingChild = this.onlyOneShowingChild(route.children, route)

          if (route.children && onlyOneShowingChild && !route.alwaysShow) {
            route = onlyOneShowingChild
          }

          const data = {
            path: path.resolve(basePath, route.path),
            title: route.name

          }

          // recursive child routes
          if (route.children) {
            data.children = this.generateRoutes(route.children, data.path)
          }
          res.push(data)
        }
        return res
      },
      // reference: src/view/layout/components/Sidebar/SidebarItem.vue
      onlyOneShowingChild(children = [], parent) {
        let onlyOneChild = null
        const showingChildren = children.filter(item => !item.hidden)

        // When there is only one child route, the child route is displayed by default
        if (showingChildren.length === 1) {
          onlyOneChild = showingChildren[0]
          onlyOneChild.path = path.resolve(parent.path, onlyOneChild.path)
          return onlyOneChild
        }

        // Show parent if there are no child route to display
        if (showingChildren.length === 0) {
          onlyOneChild = { ... parent, path: '', noShowingChildren: true }
          return onlyOneChild
        }
        return false
      },
      async setCheckedRoutes()  {
        this.checkStrictly = true
        await this.getCheckedRoutes()
        console.log(this.thisRoleMenus)
        this.$refs.tree.setCheckedNodes(this.generateArr(this.thisRoleMenus))
        this.checkStrictly = false
      },
      generateArr(routes) {
        let data = []
        routes.forEach(route => {
          data.push(route)
          if (route.children) {
            const temp = this.generateArr(route.children)
            if (temp.length > 0) {
              data = [...data, ...temp]
            }
          }
        })
        return data
      }
    }
  }
</script>

<style scoped>

</style>
