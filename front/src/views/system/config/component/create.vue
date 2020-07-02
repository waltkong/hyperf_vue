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
        <el-form-item label="组名" prop="group_name">
          <el-input v-model="defaultForm.group_name" />
        </el-form-item>
        <el-form-item label="键名" prop="name">
          <el-input v-model="defaultForm.name" />
        </el-form-item>
        <el-form-item label="值" prop="value">
          <el-input v-model="defaultForm.value" />
        </el-form-item>
        <el-form-item label="值类型" prop="type">
          <el-input v-model="defaultForm.type" />
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

  import { storeOrUpdate, getOne } from '@/api/system/config_api'
  import { isApiSuccess } from '@/configs/apicode'

  export default {
    name: 'ConfigCreate',
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
          group_name: 'default',
          value: '',
          type: '',
        },
        rules: {
          name: [{ required: true, message: '名称必填', trigger: 'blur' }]
        }
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
        this.title = '配置新增'
      } else {
        this.title = '配置编辑'
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
          console.log(response)
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
      }
    }
  }
</script>

<style scoped>

</style>
