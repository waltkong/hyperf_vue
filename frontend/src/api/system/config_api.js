import request from '@/utils/request'

export function dataList(data) {
  return request({
    url: '/admin/config/dataList',
    method: 'post',
    data
  })
}

export function storeOrUpdate(data) {
  return request({
    url: '/admin/config/storeOrUpdate',
    method: 'post',
    data
  })
}

export function getOne(data) {
  return request({
    url: '/admin/config/getOne',
    method: 'post',
    data
  })
}

export function deleteOne(data) {
  return request({
    url: '/admin/config/deleteOne',
    method: 'post',
    data
  })
}
