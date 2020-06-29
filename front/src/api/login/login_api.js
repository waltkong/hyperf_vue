import request from '@/utils/request'

export function login(data) {
  return request({
    url: '/admin/login/login',
    method: 'post',
    data
  })
}

export function logout(data) {
  return request({
    url: '/admin/login/logout',
    method: 'post',
    data
  })
}

