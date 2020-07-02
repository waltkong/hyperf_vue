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
        <el-form-item label="名 称" prop="name">
          <el-input v-model="defaultForm.name" />
        </el-form-item>

        <el-form-item label="url" prop="url">
          <el-input v-model="defaultForm.url" />
        </el-form-item>

        <el-form-item label="父 级" prop="parent_id">
          <el-select v-model="defaultForm.parent_id" class="filter-item" placeholder="">
            <el-option v-for="item in menuParentOptions" :key="item.key" :label="item.label" :value="item.key" />
          </el-select>
        </el-form-item>

        <el-form-item label="权限验证" prop="need_auth">
          <el-select v-model="defaultForm.need_auth" class="filter-item" placeholder="">
            <el-option v-for="item in needAuthOptions" :key="item.key" :label="item.label" :value="item.key" />
          </el-select>
        </el-form-item>

        <el-form-item label="是否菜单" prop="is_menu">
          <el-select v-model="defaultForm.is_menu" class="filter-item" placeholder="">
            <el-option v-for="item in isMenuOptions" :key="item.key" :label="item.label" :value="item.key" />
          </el-select>
        </el-form-item>

        <el-form-item label="仅允许超管理员" prop="need_auth">
          <el-select v-model="defaultForm.is_only_super_admin" class="filter-item" placeholder="">
            <el-option v-for="item in isOnlySuperAdminOptions" :key="item.key" :label="item.label" :value="item.key" />
          </el-select>
        </el-form-item>

        <el-form-item label="仅允许超管公司" prop="need_auth">
          <el-select v-model="defaultForm.is_only_super_company" class="filter-item" placeholder="">
            <el-option v-for="item in isOnlySuperCompanyOptions" :key="item.key" :label="item.label" :value="item.key" />
          </el-select>
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

  import { storeOrUpdate, getOne , menuParentOptions } from '@/api/system/menu_api'
  import { isApiSuccess } from '@/configs/apicode'

  const needAuthOptions = [
    { key: '1', label: '是' },
    { key: '0', label: '否' }
  ];

  const isMenuOptions = [
    { key: '1', label: '是' },
    { key: '0', label: '否' }
  ];

  const isOnlySuperAdminOptions = [
    { key: '1', label: '是' },
    { key: '0', label: '否' }
  ];

  const isOnlySuperCompanyOptions = [
    { key: '1', label: '是' },
    { key: '0', label: '否' }
  ];

  export default {
    name: 'MenuCreate',
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
          name: '',
          url: '',
          parent_id: '',
          need_auth: '',
          is_menu: '',
          is_only_super_admin: '',
          is_only_super_company: '',
        },
        rules: {
          name: [{ required: true, message: '名称必填', trigger: 'blur' }],
          url: [{ required: true, message: 'url必填', trigger: 'blur' }],
        },
        needAuthOptions,
        isMenuOptions,
        isOnlySuperAdminOptions,
        isOnlySuperCompanyOptions,
        menuParentOptions: [],
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

      this.getMenuParentOptions();

      if (this.editOrCreate === 'create'){
        this.title = '菜单新增'
      } else {
        this.title = '菜单编辑'
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
              name: response.data.data.name,
              group_name: response.data.data.group_name,
              value: response.data.data.value,
              type: response.data.data.type
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
      getMenuParentOptions() {
        menuParentOptions().then(response => {
          if (isApiSuccess(response.code)){
            this.menuParentOptions = response.data.data
          } else {
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
