import React, { useCallback, useEffect, useState } from 'react';
import { Alert } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { ActionCard } from '../components/Card';
import { SectionTitle } from '../components/DashboardUi';
import { AccountProfileCard, AppInfoFooter, DangerActionCard } from '../components/AccountUi';
import { AuthApi } from '../api/auth';
import { logoutSession } from '../auth/authSession';
import { formatStudentDisplayId } from '../utils/studentId';
import { LmsApi } from '../api/lms';
import { AppStorage } from '../storage/AppStorage';
import { APP_SUBTITLE, APP_TITLE } from '../config';

type StoredUser = {
  child_name?: string;
  student_id?: string;
};

export default function AccountHomeScreen() {
  const navigation = useNavigation<any>();
  const [loading, setLoading] = useState(true);
  const [user, setUser] = useState<StoredUser | null>(null);
  const [child, setChild] = useState<any>(null);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const [storedUser, dashRes]: [StoredUser | null, any] = await Promise.all([
        AppStorage.getUser<StoredUser>(),
        LmsApi.dashboard(),
      ]);
      setUser(storedUser);
      setChild((dashRes.dashboard ?? dashRes)?.child ?? null);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  async function logout() {
    Alert.alert('Logout', 'Sign out of this device?', [
      { text: 'Cancel', style: 'cancel' },
      {
        text: 'Logout',
        style: 'destructive',
        onPress: async () => {
          try {
            await AuthApi.logout();
          } catch {
            /* ignore */
          }
          await logoutSession();
        },
      },
    ]);
  }

  const childName = child?.name ?? user?.child_name ?? 'Your child';
  const studentId = formatStudentDisplayId(user?.student_id ?? child?.id);

  return (
    <ScreenLayout title="Account" refreshing={loading} onRefresh={load}>
      <AccountProfileCard
        portalLabel={APP_SUBTITLE}
        title={`Parent of ${childName}`}
        subtitle="Manage your account and keep your login secure"
        avatarLabel={childName}
        chips={[
          { label: `Student ID ${studentId ?? '—'}`, icon: 'card-outline' },
          { label: child?.course_name ?? 'Course —', icon: 'school-outline' },
          ...(child?.batch ? [{ label: `Batch ${child.batch}`, icon: 'people-outline' }] : []),
        ]}
      />

      <SectionTitle>Security</SectionTitle>
      <ActionCard
        iconName="lock-closed-outline"
        iconFamily="ionicons"
        title="Change password"
        subtitle="Update your login password"
        accent="#e0f2fe"
        onPress={() => navigation.navigate('ChangePassword')}
      />

      <SectionTitle>Session</SectionTitle>
      <DangerActionCard
        iconName="log-out-outline"
        title="Logout"
        subtitle="Sign out from this device"
        onPress={logout}
      />

      <AppInfoFooter appTitle={APP_TITLE} portalLabel={APP_SUBTITLE} version="0.0.1" />
    </ScreenLayout>
  );
}
