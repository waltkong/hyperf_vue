import request from '@/utils/request'

export function dataList(data) {
  return request({
    url: '/admin/user/dataList',
    method: 'post',
    data
  })
}

export function storeOrUpdate(data) {
  return request({
    url: '/admin/user/storeOrUpdate',
    method: 'post',
    data
  })
}

export function getOne(data) {
  return request({
    url: '/admin/user/getOne',
    method: 'post',
    data
  })
}

export function deleteOne(data) {
  return request({
    url: '/admin/user/deleteOne',
    method: 'post',
    data
  })
}

// 获取当前用户信息
export function userInfo(data) {
  return request({
    url: '/admin/user/userInfo',
    method: 'post',
    data
  })
}

