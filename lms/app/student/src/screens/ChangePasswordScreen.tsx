import React, { useState } from 'react';
import { Text, Pressable, StyleSheet, Alert, View } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { FormInput } from '../components/FormInput';
import AppIcon from '../components/AppIcon';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';

export default function ChangePasswordScreen() {
  const navigation = useNavigation<any>();
  const [current, setCurrent] = useState('');
  const [next, setNext] = useState('');
  const [loading, setLoading] = useState(false);

  async function save() {
    if (!current || !next) {
      Alert.alert('Error', 'Fill all fields');
      return;
    }
    setLoading(true);
    try {
      await LmsApi.changePassword({ current_password: current, new_password: next });
      Alert.alert('Success', 'Password updated');
      setCurrent('');
      setNext('');
      navigation.goBack();
    } catch (e: any) {
      Alert.alert('Error', e?.message ?? 'Failed');
    } finally {
      setLoading(false);
    }
  }

  return (
    <ScreenLayout
      title="Change Password"
      subtitle="Account security"
      onBack={() => navigation.goBack()}>
      <Card>
        <View style={styles.hintRow}>
          <View style={styles.hintIcon}>
            <AppIcon name="shield-checkmark-outline" size={22} color={PRIMARY} />
          </View>
          <Text style={styles.hintText}>
            Choose a strong password you have not used elsewhere.
          </Text>
        </View>

        <FormInput
          label="Current password"
          value={current}
          onChangeText={setCurrent}
          placeholder="Enter current password"
          secureTextEntry
        />
        <FormInput
          label="New password"
          value={next}
          onChangeText={setNext}
          placeholder="Enter new password"
          secureTextEntry
        />

        <Pressable style={[styles.btn, loading && styles.disabled]} onPress={save} disabled={loading}>
          <Text style={styles.btnText}>{loading ? 'Saving…' : 'Update password'}</Text>
        </Pressable>
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  hintRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
    backgroundColor: theme.primarySoft,
    borderRadius: 14,
    padding: 14,
    marginBottom: 8,
  },
  hintIcon: {
    width: 40,
    height: 40,
    borderRadius: 12,
    backgroundColor: theme.card,
    alignItems: 'center',
    justifyContent: 'center',
  },
  hintText: {
    flex: 1,
    fontSize: 13,
    color: theme.muted,
    lineHeight: 18,
  },
  btn: {
    backgroundColor: PRIMARY,
    padding: 15,
    borderRadius: 14,
    alignItems: 'center',
    marginTop: 8,
    shadowColor: PRIMARY,
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.25,
    shadowRadius: 10,
    elevation: 3,
  },
  btnText: { color: '#fff', fontWeight: '800', fontSize: 15 },
  disabled: { opacity: 0.6 },
});
