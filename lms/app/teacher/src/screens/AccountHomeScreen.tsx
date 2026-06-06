import React from 'react';
import { Alert } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { ActionCard, Card } from '../components/Card';
import { AuthApi } from '../api/auth';
import { logoutSession } from '../auth/authSession';

export default function AccountHomeScreen() {
  const navigation = useNavigation<any>();

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
    <ScreenLayout title="Account">
      <ActionCard
        iconName="wallet-outline"
        title="View salary"
        subtitle="Monthly salary records"
        onPress={() => navigation.navigate('Salary')}
      />
      <ActionCard
        iconName="lock-reset"
        title="Change password"
        subtitle="Update account security"
        onPress={() => navigation.navigate('ChangePassword')}
      />
      <ActionCard
        iconName="logout"
        title="Logout"
        subtitle="Sign out of your account"
        onPress={logout}
      />
    </ScreenLayout>
  );
}
