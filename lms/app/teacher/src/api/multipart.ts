import { API_BASE_URL } from '../config';
import { AppStorage } from '../storage/AppStorage';
import type { ApiError } from './http';

async function sendMultipart(
  method: 'POST' | 'PATCH',
  path: string,
  form: FormData,
  timeoutMs = 120000,
): Promise<unknown> {
  const token = await AppStorage.getToken();
  const headers: Record<string, string> = {
    Accept: 'application/json',
  };
  if (token) {
    headers.Authorization = `Bearer ${token}`;
  }

  const controller = new AbortController();
  const timeout = setTimeout(() => controller.abort(), timeoutMs);

  try {
    const response = await fetch(`${API_BASE_URL}${path}`, {
      method,
      headers,
      body: form,
      signal: controller.signal,
    });

    const raw = await response.text();
    let data: Record<string, unknown> = {};
    try {
      data = raw ? (JSON.parse(raw) as Record<string, unknown>) : {};
    } catch {
      throw { status: response.status, message: raw || 'Upload failed' } satisfies ApiError;
    }

    if (!response.ok) {
      const errors = data.errors as Record<string, string[]> | undefined;
      const first = errors ? Object.values(errors).flat()[0] : undefined;
      throw {
        status: response.status,
        message: String(first || data.message || 'Upload failed'),
      } satisfies ApiError;
    }

    return data;
  } catch (error: unknown) {
    const apiError = error as ApiError;
    if (apiError?.status !== undefined && apiError?.message) {
      throw apiError;
    }
    const err = error as { name?: string; message?: string };
    const isNetworkFailure =
      !apiError?.status &&
      (err?.name === 'AbortError' ||
        /network request failed/i.test(err?.message ?? '') ||
        /failed to fetch/i.test(err?.message ?? ''));
    throw {
      status: null,
      message: err?.name === 'AbortError'
        ? 'Upload timed out'
        : isNetworkFailure
          ? 'Upload failed — check your connection and try a smaller file (max 20 MB).'
          : err?.message || 'Upload failed',
    } satisfies ApiError;
  } finally {
    clearTimeout(timeout);
  }
}

export function postMultipart(
  path: string,
  form: FormData,
  timeoutMs = 120000,
): Promise<unknown> {
  return sendMultipart('POST', path, form, timeoutMs);
}

export function patchMultipart(path: string, form: FormData, timeoutMs = 120000): Promise<unknown> {
  return sendMultipart('PATCH', path, form, timeoutMs);
}
