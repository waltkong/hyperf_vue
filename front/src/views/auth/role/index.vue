<template>
  <div class="app-container">

    <div class="filter-container">
      <el-input
        v-model="listQuery.name"
        placeholder="Title"
        clearable
        style="width: 200px;"
        class="filter-item"
        @keyup.enter.native="handleFilter" />

      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="handleFilter">
        搜索
      </el-button>

      <el-button class="filter-item" style="margin-left: 30px;" type="primary" icon="el-icon-edit" @click="handleCreate">
        新增
      </el-button>

      <el-button v-waves :loading="downloadLoading" class="filter-item" type="primary" icon="el-icon-download" @click="handleDownload">
        导出
      </el-button>
    </div>

    <el-table
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      @sort-change="sortChange"
    >
      <el-table-column label="ID" prop="id" sortable="custom" align="center" width="80" :class-name="getSortClass('id')">
        <template slot-scope="{row}">
          <span>{{ row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="名称" min-width="150px">
        <template slot-scope="{row}">
          <span>{{ row.name }}</span>
        </template>
      </el-table-column>
      <el-table-column label="公司" width="110px" align="center">
        <template slot-scope="{row}">
          <span>{{ row.company_name }}</span>
        </template>
      </el-table-column>
      <el-table-column label="创建时间" width="150px" align="center">
        <template slot-scope="{row}">
          <span>{{ row.created_at }}</span>
        </template>
      </el-table-column>
      <el-table-column label="操作" align="center" width="230" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <el-button type="primary" size="mini" @click="handleEdit(row)">
            编辑
          </el-button>
          <el-button size="mini" type="danger" @click="handleDelete(row)">
            删除
          </el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page_index" :limit.sync="listQuery.each_page" @pagination="getList" />

    <create-form
      v-if="createVisible"
      ref="createForm"
      :create-visible.sync="createVisible"
      :edit-row="thisEditRow"
      :edit-or-create="editOrCreate"
      @getList="getList"
    />

  </div>
</template>

<script>
import waves from '@/directive/waves' // waves directive
import Pagination from '@/components/Pagination'
import CreateForm from './component/create'
import { dataList, deleteOne } from '@/api/auth/role_api'
import { isApiSuccess } from '@/configs/apicode'

export default {
  name: 'RoleIndex',
  components: { Pagination, CreateForm },
  directives: { waves },
  data() {
    return {
      tableKey: 0,
      list: null,
      total: 0,
      listLoading: true,
      listQuery: {
        page_index: 1,
        each_page: 20,
        order_by: 'id',
        order_way: 'desc',
        name: undefined
      },
      downloadLoading: false,
      createVisible: false,
      thisEditRow: {},
      editOrCreate: 'create'
    }
  },
  created() {
    this.getList()
  },
  methods: {
    getList() {
      this.listLoading = true
      dataList(this.listQuery).then(response => {
        this.list = response.data.data
        this.total = response.data.total

        // Just to simulate the time of the request
        setTimeout(() => {
          this.listLoading = false
        }, 0.1 * 1000)
      })
    },
    handleFilter() {
      this.listQuery.page_index = 1
      this.getList()
    },
    sortChange(data) {
      const { prop, order } = data
      this.listQuery.order_by = prop
      this.listQuery.order_way = order
      this.listQuery.page_index = 1
      this.getList()
    },
    handleCreate() {
      this.thisEditRow = {}
      this.editOrCreate = 'create'
      this.createVisible = true
    },
    handleEdit(row) {
      this.thisEditRow = row
      this.editOrCreate = 'edit'
      this.createVisible = true
    },
    handleDelete(row) {
      this.$confirm(`删除`, '确认删除？', { confirmButtonText: '确定',
        cancelButtonText: '取消',
        dangerouslyUseHTMLString: true,
        center: true }).then(() => {
        // 手机
        this.deleteApiRequest(row)
      })
      this.getList()
    },
    deleteApiRequest(row) {
      this.listLoading = true
      const ajaxData = {
        id: row.id
      }
      deleteOne(ajaxData).then(response => {
        if (isApiSuccess(response.code)) {
          this.$message({
            message: '删除成功',
            type: 'success',
            duration: 1500
          })
          this.getList()
        } else {
          this.$message({
            message: response.msg || '异常错误',
            type: 'error',
            duration: 3000
          })
        }
        this.listLoading = false
      })
    },
    handleDownload() {
      this.downloadLoading = true
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['id', '名称', '备注', '公司', '创建时间']
        const filterVal = ['id', 'name', 'remark', 'company_name', 'created_at']
        const data = this.formatJson(filterVal)
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'default-download'
        })
        this.downloadLoading = false
      })
    },
    formatJson(filterVal) {
      return this.list.map(v => filterVal.map(j => {
        return v[j]
      }))
    },
    getSortClass: function(key) {
      const sort = this.listQuery.order_by
      return sort === `+${key}` ? 'ascending' : 'descending'
    }
  }
}
</script>

<style scoped>

</style>
