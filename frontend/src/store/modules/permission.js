import { asyncRoutes, constantRoutes } from '@/router'

/**
 * meta.auth_api 决定用户的路由权限
 * @param auth_api [所有url权限]
 * @param route
 */
function hasPermission(auth_api, route) {
  if (route.meta && route.meta.auth_api && route.meta.auth_api !== '') {
    let result = false
    auth_api.forEach(function(v, i) {
      if (typeof route.meta.auth_api === 'string') {
        if (v.toString() === route.meta.auth_api){
          result = true
        }
      }
    })
    return result
  } else {
    return true
  }
}

/**
 * Filter asynchronous routing tables by recursion
 * @param routes asyncRoutes
 * @param auth_api 所有权限
 */
export function filterAsyncRoutes(routes, auth_api) {
  const res = []

  routes.forEach(route => {
    const tmp = { ...route }
    if (hasPermission(auth_api, tmp)) {
      if (tmp.children) {
        tmp.children = filterAsyncRoutes(tmp.children, auth_api)
      }
      res.push(tmp)
    }
  })

  return res
}

const state = {
  routes: [],
  addRoutes: []
}

const mutations = {
  SET_ROUTES: (state, routes) => {
    state.addRoutes = routes
    state.routes = constantRoutes.concat(routes)
  }
}

const actions = {
  generateRoutes({ commit }, auth_api) {
    return new Promise(resolve => {
      let accessedRoutes
      if (auth_api === '*') {
        accessedRoutes = asyncRoutes || []
      } else {
        accessedRoutes = filterAsyncRoutes(asyncRoutes, auth_api)
      }
      commit('SET_ROUTES', accessedRoutes)
      resolve(accessedRoutes)
    })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
