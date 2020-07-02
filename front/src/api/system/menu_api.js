import request from '@/utils/request'

export function dataList(data) {
  return request({
    url: '/admin/menu/dataList',
    method: 'post',
    data
  })
}

export function storeOrUpdate(data) {
  return request({
    url: '/admin/menu/storeOrUpdate',
    method: 'post',
    data
  })
}

export function getOne(data) {
  return request({
    url: '/admin/menu/getOne',
    method: 'post',
    data
  })
}

export function deleteOne(data) {
  return request({
    url: '/admin/menu/deleteOne',
    method: 'post',
    data
  })
}

export function menuParentOptions(data) {
  return request({
    url: '/admin/menu/menuParentOptions',
    method: 'post',
    data
  })
}


