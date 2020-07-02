<template>
  <div>
    <el-dialog
      :title="title"
      center
      width="70%"
      :visible.sync="dialogVisible"
      :fullscreen="fullscreen"
    >
      <el-form ref="dataForm" :rules="rules" :model="defaultForm" label-position="left" label-width="70px" style="width: 400px; margin-left:50px;">
        <el-form-item label="昵称" prop="nickname">
          <el-input v-model="defaultForm.nickname" />
        </el-form-item>
        <el-form-item label="手机" prop="mobile">
          <el-input v-model="defaultForm.mobile" />
        </el-form-item>

        <el-form-item label="密码" prop="password" v-if="editOrCreate === 'create'">
          <el-input v-model="defaultForm.password" />
        </el-form-item>

        <el-form-item label="类型" prop="admin_status">
          <el-select v-model="defaultForm.admin_status" class="filter-item" placeholder="">
            <el-option v-for="item in adminStatusOptions" :key="item.key" :label="item.label" :value="item.key" />
          </el-select>
        </el-form-item>

        <el-form-item label="角色" prop="roles">
          <el-checkbox-group v-model="roleType">
            <el-checkbox  v-for="item in roleOptions" :key="item.key"  :label="item.label"  :value="item.key"  />
          </el-checkbox-group>
        </el-form-item>

      </el-form>

      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">取 消</el-button>
        <el-button @click="handleCreate">提 交</el-button>
      </div>

    </el-dialog>
  </div>
</template>

<script>

  import { storeOrUpdate, getOne } from '@/api/auth/user_api'
  import { thisCompanyRoleOptions } from '@/api/auth/role_api'
  import { isApiSuccess } from '@/configs/apicode'

  const adminStatusOptions = [
    { key: '1', label: '超管' },
    { key: '2', label: '普通' }
  ]
  export default {
    name: 'UserCreate',
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
    data() {
      return {
        loading: false,
        title: '',
        dialogVisible: this.createVisible,
        fullscreen: false,
        defaultForm: {
          id: '',
          nickname: '',
          password:'',
          mobile: '',
          admin_status: '2',
          roleIds: ''
        },
        roleType:[],   //无法放到defaultForm里，会有bug,所以提出来
        rules: {
          name: [{ required: true, message: '名称必填', trigger: 'blur' }]
        },
        adminStatusOptions,
        roleOptions:[],
      }
    },
    watch: {
      dialogVisible(newValue, oldValue) {
        this.$emit('update:createVisible', newValue)
      },
      createVisible(newValue, oldValue) {
        this.dialogVisible = newValue
      },
      roleType(newValue, oldValue) {
        this.defaultForm.roleIds = newValue
      }
    },
    created() {
      if (this.$store.state.app.device === 'mobile') {
        this.fullscreen = true
      }

      this.getRoleOptions();

      if (this.editOrCreate === 'create'){
        this.title = '用户新增'
      } else {
        this.title = '用户编辑'
        this.getFormData(this.editRow.id)
      }
    },
    methods: {
      // 执行创建
      handleCreate() {
        this.$refs['dataForm'].validate(valid => {
          if (valid) {
            storeOrUpdate(this.defaultForm).then(response => {
              if (isApiSuccess(response.code)) {
                this.$message({
                  message: 'ok',
                  type: 'success',
                  duration: 1500
                })
                this.dialogVisible = false
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
          if (isApiSuccess(response.code)) {
            this.defaultForm = {
              id: response.data.data.id,
              nickname: response.data.data.nickname,
              password: '',
              mobile: response.data.data.mobile,
              admin_status: response.data.data.admin_status.toString(),
              roleIds: response.data.data.roleIds
            }
            this.roleType = response.data.data.roleIds
          } else {
            this.$message({
              message: response.msg || 'error',
              type: 'error',
              duration: 3000
            })
          }
        })
      },
      getRoleOptions() {
        thisCompanyRoleOptions().then(response => {
          console.log(response)
          if (isApiSuccess(response.code)) {
            this.roleOptions = response.data.data
          }else {
            this.$message({
              message: response.msg || 'error',
              type: 'error',
              duration: 3000
            })
          }
        })
      }
    }
  }
</script>

<style scoped>

</style>

