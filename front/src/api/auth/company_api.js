import request from '@/utils/request'

export function dataList(data) {
  return request({
    url: '/admin/company/dataList',
    method: 'post',
    data
  })
}

export function storeOrUpdate(data) {
  return request({
    url: '/admin/company/storeOrUpdate',
    method: 'post',
    data
  })
}

export function getOne(data) {
  return request({
    url: '/admin/company/getOne',
    method: 'post',
    data
  })
}

export function deleteOne(data) {
  return request({
    url: '/admin/company/deleteOne',
    method: 'post',
    data
  })
}

