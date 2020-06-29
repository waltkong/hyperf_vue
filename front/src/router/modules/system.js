import Layout from '@/layout'

const systemRouter = {
  path: '/system',
  component: Layout,
  redirect: '/system/menu',
  meta: { title: '系统管理', icon: 'dashboard', apiAuth: '' },
  children: [
    {
      path: 'menu',
      name: 'Menu',
      component: () => import('@/views/system/menu/index'),
      meta: { title: '菜单管理', icon: 'dashboard', apiAuth: '/admin/menu/dataList' }
    },
    {
      path: 'config',
      name: 'Config',
      component: () => import('@/views/system/config/index'),
      meta: { title: '全局配置', icon: 'dashboard', apiAuth: '/admin/config/dataList' }
    },
    {
      path: 'log',
      name: 'Log',
      component: () => import('@/views/system/log/index'),
      meta: { title: '操作日志', icon: 'dashboard', apiAuth: '/admin/log/dataList' }
    }
  ]
}

export default systemRouter
