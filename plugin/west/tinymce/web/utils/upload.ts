/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
export function uploadLocal(options: any, progress: (percent: number) => void) {
  const upload = (formData: FormData) => {
    return useHttp().post('/admin/attachment/upload', formData)
  }

  return new Promise((resolve, reject) => {
    const formData = new FormData()
    formData.append('file', options.blob())
    upload(formData).then((res: Record<string, any>) => {
      res.code === 200 ? resolve(res.data.url) : reject(res)
    }).catch((err) => {
      reject(err)
    })
  })
}
