import React from 'react';
import { Text, StyleSheet, View } from 'react-native';
import { theme } from '../ui/theme';

export default function DetailRow({ label, value }: { label: string; value?: string | number | null }) {
  return (
    <View style={styles.row}>
      <Text style={styles.label}>{label}</Text>
      <Text style={styles.value}>{value ?? '—'}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  row: { marginBottom: 12 },
  label: { fontSize: 12, fontWeight: '600', color: theme.muted, marginBottom: 2 },
  value: { fontSize: 15, color: theme.text },
});
