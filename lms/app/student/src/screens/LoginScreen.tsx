import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  Pressable,
  StyleSheet,
  ActivityIndicator,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { AuthApi } from '../api/auth';
import { loginSession } from '../auth/authSession';
import { loginStudentIdError } from '../utils/studentId';
import {
  APP_SUBTITLE,
  APP_TITLE,
  LOGIN_FIELDS,
  PRIMARY,
} from '../config';
import { useThemeColors, textInputStyle } from '../ui/useThemeColors';
import { platformWeight } from '../ui/typography';
import { RootStackParamList } from '../navigation/types';
import AppIcon from '../components/AppIcon';

type Props = NativeStackScreenProps<RootStackParamList, 'Login'>;

export default function LoginScreen(_props: Props) {
  const colors = useThemeColors();
  const [id, setId] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  async function onSubmit() {
    setError('');
    if (!id.trim() || !password) {
      setError('Please fill all fields');
      return;
    }
    const idError = loginStudentIdError(id);
    if (idError) {
      setError(idError);
      return;
    }
    setLoading(true);
    try {
      const body = LOGIN_FIELDS.useEmail
        ? { email: id.trim(), password }
        : { student_id: id.trim(), password };
      const res = await AuthApi.login(body);
      if (res.status !== 'success' || !res.token) {
        throw new Error('Login failed');
      }
      await loginSession(res.token, res.user);
    } catch (e: any) {
      setError(e?.message || 'Login failed');
    } finally {
      setLoading(false);
    }
  }

  return (
    <SafeAreaView style={[styles.safe, { backgroundColor: colors.bg }]}>
      <KeyboardAvoidingView
        style={styles.flex}
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        keyboardVerticalOffset={Platform.OS === 'ios' ? 0 : 24}>
        <ScrollView
          contentContainerStyle={styles.scroll}
          keyboardShouldPersistTaps="handled"
          showsVerticalScrollIndicator={false}>
          <View style={styles.header}>
            <Text style={styles.brand}>{APP_TITLE}</Text>
            <Text style={[styles.sub, { color: colors.muted }]}>{APP_SUBTITLE}</Text>
          </View>
          <View style={[styles.card, { backgroundColor: colors.card }]}>
            <Text style={[styles.label, { color: colors.text }]}>{LOGIN_FIELDS.idLabel}</Text>
            <TextInput
              style={[textInputStyle(colors), styles.inputSpacing]}
              placeholder={LOGIN_FIELDS.idPlaceholder}
              placeholderTextColor={colors.muted}
              value={id}
              onChangeText={setId}
              autoCapitalize={LOGIN_FIELDS.useEmail ? 'none' : 'characters'}
              keyboardType={LOGIN_FIELDS.useEmail ? 'email-address' : 'default'}
              keyboardAppearance="light"
              autoCorrect={false}
            />
            <Text style={[styles.label, { color: colors.text }]}>Password</Text>
            <View style={styles.passwordWrap}>
              <TextInput
                style={[textInputStyle(colors), styles.passwordInput]}
                placeholder="Password"
                placeholderTextColor={colors.muted}
                secureTextEntry={!showPassword}
                value={password}
                onChangeText={setPassword}
                keyboardAppearance="light"
                autoCapitalize="none"
                autoCorrect={false}
              />
              <Pressable
                style={styles.eyeBtn}
                onPress={() => setShowPassword(v => !v)}
                hitSlop={8}
                accessibilityRole="button"
                accessibilityLabel={showPassword ? 'Hide password' : 'Show password'}>
                <AppIcon
                  name={showPassword ? 'eye-off-outline' : 'eye-outline'}
                  size={22}
                  color={colors.muted}
                />
              </Pressable>
            </View>
            {error ? <Text style={styles.error}>{error}</Text> : null}
            <Pressable
              style={[styles.btn, loading && styles.btnDisabled]}
              onPress={onSubmit}
              disabled={loading}>
              {loading ? (
                <ActivityIndicator color="#fff" />
              ) : (
                <Text style={styles.btnText}>Sign In</Text>
              )}
            </Pressable>
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safe: { flex: 1 },
  flex: { flex: 1 },
  scroll: {
    flexGrow: 1,
    justifyContent: 'center',
    padding: 24,
    paddingBottom: 40,
  },
  header: { alignItems: 'center', marginBottom: 28 },
  brand: { fontSize: 26, ...platformWeight('800'), color: PRIMARY },
  sub: { fontSize: 15, marginTop: 6 },
  card: {
    borderRadius: 16,
    padding: 22,
    shadowColor: '#000',
    shadowOpacity: 0.08,
    shadowRadius: 12,
    elevation: 4,
  },
  label: { fontSize: 13, ...platformWeight('600'), marginBottom: 6 },
  inputSpacing: { marginBottom: 14 },
  passwordWrap: { position: 'relative', marginBottom: 14 },
  passwordInput: { paddingRight: 48 },
  eyeBtn: {
    position: 'absolute',
    right: 12,
    top: 0,
    bottom: 0,
    justifyContent: 'center',
  },
  btn: {
    backgroundColor: PRIMARY,
    borderRadius: 10,
    paddingVertical: 14,
    alignItems: 'center',
    marginTop: 8,
  },
  btnDisabled: { opacity: 0.7 },
  btnText: { color: '#fff', ...platformWeight('700'), fontSize: 16 },
  error: { color: '#dc2626', marginBottom: 8, fontSize: 14 },
});
