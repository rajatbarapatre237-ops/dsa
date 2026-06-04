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
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { AuthApi } from '../api/auth';
import { AppStorage } from '../storage/AppStorage';
import {
  APP_SUBTITLE,
  APP_TITLE,
  LOGIN_FIELDS,
  PRIMARY,
} from '../config';
import { theme } from '../ui/theme';
import { RootStackParamList } from '../navigation/types';

type Props = NativeStackScreenProps<RootStackParamList, 'Login'>;

export default function LoginScreen({ navigation }: Props) {
  const [id, setId] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  async function onSubmit() {
    setError('');
    if (!id.trim() || !password) {
      setError('Please fill all fields');
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
      await AppStorage.setToken(res.token);
      await AppStorage.setUser(res.user);
      navigation.replace('Main');
    } catch (e: any) {
      setError(e?.message || 'Login failed');
    } finally {
      setLoading(false);
    }
  }

  return (
    <KeyboardAvoidingView
      style={styles.wrap}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <View style={styles.header}>
        <Text style={styles.brand}>{APP_TITLE}</Text>
        <Text style={styles.sub}>{APP_SUBTITLE}</Text>
      </View>
      <View style={styles.card}>
        <Text style={styles.label}>{LOGIN_FIELDS.idLabel}</Text>
        <TextInput
          style={styles.input}
          placeholder={LOGIN_FIELDS.idPlaceholder}
          value={id}
          onChangeText={setId}
          autoCapitalize={LOGIN_FIELDS.useEmail ? 'none' : 'characters'}
          keyboardType={LOGIN_FIELDS.useEmail ? 'email-address' : 'default'}
        />
        <Text style={styles.label}>Password</Text>
        <TextInput
          style={styles.input}
          placeholder="Password"
          secureTextEntry
          value={password}
          onChangeText={setPassword}
        />
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
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  wrap: { flex: 1, backgroundColor: theme.bg, justifyContent: 'center', padding: 24 },
  header: { alignItems: 'center', marginBottom: 28 },
  brand: { fontSize: 26, fontWeight: '800', color: PRIMARY },
  sub: { fontSize: 15, color: theme.muted, marginTop: 6 },
  card: {
    backgroundColor: theme.card,
    borderRadius: 16,
    padding: 22,
    shadowColor: '#000',
    shadowOpacity: 0.08,
    shadowRadius: 12,
    elevation: 4,
  },
  label: { fontSize: 13, fontWeight: '600', color: theme.text, marginBottom: 6 },
  input: {
    borderWidth: 1,
    borderColor: '#e2e8f0',
    borderRadius: 10,
    paddingHorizontal: 14,
    paddingVertical: 12,
    marginBottom: 14,
    fontSize: 16,
  },
  btn: {
    backgroundColor: PRIMARY,
    borderRadius: 10,
    paddingVertical: 14,
    alignItems: 'center',
    marginTop: 8,
  },
  btnDisabled: { opacity: 0.7 },
  btnText: { color: '#fff', fontWeight: '700', fontSize: 16 },
  error: { color: theme.danger, marginBottom: 8, fontSize: 14 },
});
