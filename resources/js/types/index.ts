import type { Page } from '@inertiajs/core'

export * from './auth';
export * from './navigation';
export * from './ui';

export interface PageProps extends Record<string, unknown> {  // ✅ extend Record instead
  flash: {
    success?: string | null
    error?: string | null
  }
  auth: {
    user: {
      id: number
      name: string
      email: string
      role: string
    }
  }
}

export type InertiaPage<TProps extends PageProps = PageProps> = Page<TProps>
