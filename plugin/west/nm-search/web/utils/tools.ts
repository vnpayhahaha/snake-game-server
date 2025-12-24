// utils/tools.ts
export const formComponentMap = (() => {
  const modules = import.meta.glob('$/west/nm-search/components/searchFormItem/form-*.vue', {
    eager: true,
    import: 'default',
  })
  return Object.fromEntries(
    Object.entries(modules).map(([key, component]) => {
      const name = key.split('/').pop()?.replace('form-', '').replace('.vue', '')
      return [name, component]
    }),
  )
})()

export function getComponentByRender(render: any): any {
  // eslint-disable-next-line style/max-statements-per-line
  if (!render) { return null }
  if (typeof render === 'function' && render.name && render.name !== 'SearchButton') {
    return render()
  }
  return formComponentMap[render] || null
}

export function resolveLabel(label: string | (() => string)): string {
  return typeof label === 'function' ? label() : label
}
