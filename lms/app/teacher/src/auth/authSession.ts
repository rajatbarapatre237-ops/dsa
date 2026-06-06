import { AppStorage } from '../storage/AppStorage';
import { resetToLogin } from '../navigation/RootNavigation';

type AuthListener = (loggedIn: boolean) => void;

const listeners = new Set<AuthListener>();
let loggingOut = false;

function notify(loggedIn: boolean) {
  listeners.forEach(listener => listener(loggedIn));
}

export function subscribeAuth(listener: AuthListener): () => void {
  listeners.add(listener);
  return () => listeners.delete(listener);
}

export async function isLoggedIn(): Promise<boolean> {
  return !!(await AppStorage.getToken());
}

export async function loginSession(token: string, user: unknown): Promise<void> {
  await AppStorage.setToken(token);
  await AppStorage.setUser(user);
  notify(true);
}

export async function logoutSession(): Promise<void> {
  if (loggingOut) {
    return;
  }
  loggingOut = true;
  try {
    await AppStorage.clear();
    notify(false);
    resetToLogin();
  } finally {
    loggingOut = false;
  }
}
