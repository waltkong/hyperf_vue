import Layout from '@/layout'

const authRouter = {
  path: '/auth',
  component: Layout,
  redirect: '/auth/role',
  meta: { title: '权限管理', icon: 'dashboard', apiAuth: '' },
  children: [
    {
      path: 'role',
      name: 'Role',
      component: () => import('@/views/auth/role/index'),
      meta: { title: '角色管理', icon: 'dashboard', apiAuth: '/admin/role/dataList' }
    },
    {
      path: 'user',
      name: 'User',
      component: () => import('@/views/auth/user/index'),
      meta: { title: '用户管理', icon: 'dashboard', apiAuth: '/admin/user/dataList' }
    },
    {
      path: 'company',
      name: 'Company',
      component: () => import('@/views/auth/company/index'),
      meta: { title: '公司管理', icon: 'dashboard', apiAuth: '/admin/company/dataList' }
    }
  ]
}

export default authRouter
