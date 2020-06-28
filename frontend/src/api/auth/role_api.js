import request from '@/utils/request'

export function dataList(data) {
  return request({
    url: '/admin/role/dataList',
    method: 'post',
    data
  })
}

export function storeOrUpdate(data) {
  return request({
    url: '/admin/role/storeOrUpdate',
    method: 'post',
    data
  })
}

export function getOne(data) {
  return request({
    url: '/admin/role/getOne',
    method: 'post',
    data
  })
}

export function deleteOne(data) {
  return request({
    url: '/admin/role/deleteOne',
    method: 'post',
    data
  })
}

