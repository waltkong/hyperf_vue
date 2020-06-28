import request from '@/utils/request'

export function dataList(data) {
  return request({
    url: '/admin/log/dataList',
    method: 'post',
    data
  })
}

export function storeOrUpdate(data) {
  return request({
    url: '/admin/log/storeOrUpdate',
    method: 'post',
    data
  })
}

export function getOne(data) {
  return request({
    url: '/admin/log/getOne',
    method: 'post',
    data
  })
}

export function deleteOne(data) {
  return request({
    url: '/admin/log/deleteOne',
    method: 'post',
    data
  })
}

