import { Platform } from 'react-native';

export type AppRole = 'student' | 'teacher' | 'parent';

export const APP_ROLE: AppRole = 'parent';
export const APP_TITLE = 'DSA Academy';
export const APP_SUBTITLE = 'Parent Portal';
export const PRIMARY = '#1172c2';
export const PRIMARY_DARK = '#0d5a9a';

export const API_BASE_URL =
  Platform.select({
    android: 'http://10.0.2.2:8000/api/v1',
    default: 'http://127.0.0.1:8000/api/v1',
  }) ?? 'http://127.0.0.1:8000/api/v1';

export const LOGIN_FIELDS = {
  idLabel: 'Student ID',
  idPlaceholder: 'ACE123 or 123',
  useEmail: false,
} as const;
