import React from 'react';
import { Alert } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { ActionCard } from '../components/Card';
import { SectionTitle, StudentContextCard } from '../components/DashboardUi';
import { AppInfoFooter, DangerActionCard } from '../components/AccountUi';
import { useStudentContext } from '../hooks/useStudentContext';
import { useRefreshStudentOnFocus } from '../hooks/useRefreshStudentOnFocus';
import { AuthApi } from '../api/auth';
import { logoutSession } from '../auth/authSession';
import { APP_SUBTITLE, APP_TITLE } from '../config';
import { theme } from '../ui/theme';

export default function AccountHomeScreen() {
  const navigation = useNavigation<any>();
  const ctx = useStudentContext();
  useRefreshStudentOnFocus();

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

  return (
    <ScreenLayout title="Account" refreshing={ctx.loading} onRefresh={ctx.refresh}>
      <StudentContextCard
        name={ctx.name}
        studentId={ctx.studentId}
        course={ctx.course}
        batch={ctx.batch}
        profile={ctx.profile}
        attendanceSummary={ctx.attendanceSummary}
        monthRecords={ctx.monthRecords}
      />

      <SectionTitle>Profile</SectionTitle>
      <ActionCard
        iconName="person-outline"
        iconFamily="ionicons"
        title="Edit profile"
        subtitle="Update your personal details"
        accent={theme.primarySoft}
        onPress={() => navigation.navigate('Profile')}
      />

      <SectionTitle>Security</SectionTitle>
      <ActionCard
        iconName="lock-closed-outline"
        iconFamily="ionicons"
        title="Change password"
        subtitle="Update your login password"
        accent={theme.primarySoft}
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
