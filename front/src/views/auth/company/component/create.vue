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
      <el-form-item label="名称" prop="name">
        <el-input v-model="defaultForm.name" />
      </el-form-item>
      <el-form-item label="联系人" prop="contact_user">
        <el-input v-model="defaultForm.contact_user" />
      </el-form-item>
      <el-form-item label="电话" prop="phone">
        <el-input v-model="defaultForm.phone" />
      </el-form-item>

      <el-form-item label="类型" prop="admin_status">
        <el-select v-model="defaultForm.admin_status" class="filter-item" placeholder="">
          <el-option v-for="item in companyAdminStatusOptions" :key="item.key" :label="item.label" :value="item.key" />
        </el-select>
      </el-form-item>

      <el-form-item label="备注" prop="remark">
        <el-input v-model="defaultForm.remark" :autosize="{ minRows: 2, maxRows: 4}" type="textarea" placeholder="" />
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

import { storeOrUpdate, getOne } from '@/api/auth/company_api'
import { isApiSuccess } from '@/configs/apicode'

const companyAdminStatusOptions = [
  { key: '1', label: '超管' },
  { key: '2', label: '普通' }
]
export default {
  name: 'CompanyCreate',
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
        phone: '',
        contact_user: '',
        admin_status: '2',
        remark: ''
      },
      rules: {
        name: [{ required: true, message: '名称必填', trigger: 'blur' }]
      },
      companyAdminStatusOptions
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

    if (this.editOrCreate === 'create'){
      this.title = '公司新增'
    } else {
      this.title = '公司编辑'
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
    }
  }
}
</script>

<style scoped>

</style>
