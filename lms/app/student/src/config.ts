import { NativeModules, Platform } from 'react-native';

export type AppRole = 'student' | 'teacher' | 'parent';

export const APP_ROLE: AppRole = 'student';
export const APP_TITLE = 'DSA Academy';
export const APP_SUBTITLE = 'Student Portal';
export const PRIMARY = '#1172c2';
export const PRIMARY_DARK = '#0d5a9a';

/** Live Laravel API (production server). */
export const LIVE_API_BASE_URL = 'https://app.dsaedu.com/api/v1';

/** Set false to use local backend during development. */
export const USE_LIVE_API = true;

/** Same Wi‑Fi fallback when USB `adb reverse` is not used (update to your Mac IP). */
export const DEV_LAN_HOST = '192.168.0.58';

function isAndroidEmulator(): boolean {
  if (Platform.OS !== 'android') {
    return false;
  }
  const constants = NativeModules.PlatformConstants as
    | {
        Fingerprint?: string;
        Model?: string;
        Brand?: string;
        Manufacturer?: string;
      }
    | undefined;

  const fingerprint = constants?.Fingerprint ?? '';
  const model = constants?.Model ?? '';
  const brand = constants?.Brand ?? '';
  const manufacturer = constants?.Manufacturer ?? '';

  return (
    fingerprint.includes('generic') ||
    fingerprint.includes('vbox') ||
    model.includes('sdk_gphone') ||
    model.includes('Emulator') ||
    model.includes('Android SDK built for') ||
    (brand === 'google' && manufacturer === 'Google' && model.startsWith('sdk_'))
  );
}

function localApiBaseUrl(): string {
  const host =
    Platform.OS === 'android'
      ? isAndroidEmulator()
        ? '10.0.2.2'
        : DEV_LAN_HOST
      : '127.0.0.1';

  return `http://${host}:8000/api/v1`;
}

export const API_BASE_URL = USE_LIVE_API ? LIVE_API_BASE_URL : localApiBaseUrl();

export const LOGIN_FIELDS = {
  idLabel: 'Student ID',
  idPlaceholder: 'DSA615 or 615',
  useEmail: false,
} as const;
