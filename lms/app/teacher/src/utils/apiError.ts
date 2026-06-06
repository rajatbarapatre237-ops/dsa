import type { ApiError } from '../api/http';

export function apiErrorMessage(error: unknown, fallback = 'Something went wrong'): string {
  if (error && typeof error === 'object' && 'message' in error) {
    const message = (error as ApiError).message;
    if (typeof message === 'string' && message.trim()) {
      return message;
    }
  }
  if (error instanceof Error && error.message.trim()) {
    return error.message;
  }
  return fallback;
}

/** Run parallel API calls without throwing if one fails. */
export async function settleApiCalls<T extends readonly Promise<unknown>[]>(
  calls: T,
): Promise<{ [K in keyof T]: T[K] extends Promise<infer R> ? R | null : null }> {
  const results = await Promise.allSettled(calls);
  return results.map(result => (result.status === 'fulfilled' ? result.value : null)) as {
    [K in keyof T]: T[K] extends Promise<infer R> ? R | null : null;
  };
}
