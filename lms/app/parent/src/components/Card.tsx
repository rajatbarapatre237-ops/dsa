import React from 'react';
import { View, Text, StyleSheet, Pressable } from 'react-native';
import { useThemeColors } from '../ui/useThemeColors';
import { PRIMARY } from '../config';
import AppIcon from './AppIcon';

type Props = {
  title?: string;
  children: React.ReactNode;
  onPress?: () => void;
};

export function Card({ title, children, onPress }: Props) {
  const colors = useThemeColors();
  const inner = (
    <View style={[styles.card, { backgroundColor: colors.card, borderColor: colors.border }]}>
      {title ? <Text style={[styles.title, { color: colors.text }]}>{title}</Text> : null}
      {children}
    </View>
  );
  if (onPress) {
    return <Pressable onPress={onPress}>{inner}</Pressable>;
  }
  return inner;
}

export function MenuTile({
  title,
  subtitle,
  onPress,
}: {
  title: string;
  subtitle?: string;
  onPress: () => void;
}) {
  const colors = useThemeColors();
  return (
    <Pressable style={({ pressed }) => [styles.tile, { backgroundColor: colors.card, borderColor: colors.border }, pressed && styles.pressed]} onPress={onPress}>
      <View style={[styles.tileIconWrap, { backgroundColor: colors.primarySoft }]}>
        <View style={styles.tileDot} />
      </View>
      <View style={styles.tileBody}>
        <Text style={[styles.tileTitle, { color: colors.text }]}>{title}</Text>
        {subtitle ? <Text style={[styles.tileSub, { color: colors.muted }]}>{subtitle}</Text> : null}
      </View>
      <AppIcon name="chevron-forward" size={20} color={colors.muted} />
    </Pressable>
  );
}

export function ActionCard({
  iconName,
  iconFamily = 'material',
  title,
  subtitle,
  onPress,
  accent = '#e8f2fc',
}: {
  iconName: string;
  iconFamily?: 'ionicons' | 'material';
  title: string;
  subtitle: string;
  onPress: () => void;
  accent?: string;
}) {
  const colors = useThemeColors();
  return (
    <Pressable
      style={({ pressed }) => [styles.actionCard, { backgroundColor: colors.card, borderColor: colors.border }, pressed && styles.pressed]}
      onPress={onPress}>
      <View style={[styles.actionIconWrap, { backgroundColor: accent }]}>
        <AppIcon name={iconName} family={iconFamily} size={24} color={PRIMARY} />
      </View>
      <View style={styles.actionBody}>
        <Text style={[styles.actionTitle, { color: colors.text }]}>{title}</Text>
        <Text style={[styles.actionSub, { color: colors.muted }]}>{subtitle}</Text>
      </View>
      <View style={[styles.actionArrow, { backgroundColor: colors.primarySoft }]}>
        <AppIcon name="chevron-forward" size={18} color={PRIMARY} />
      </View>
    </Pressable>
  );
}

const styles = StyleSheet.create({
  card: {
    borderRadius: 18,
    padding: 18,
    marginBottom: 14,
    borderWidth: 1,
    shadowColor: '#0f172a',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.05,
    shadowRadius: 12,
    elevation: 2,
  },
  title: { fontSize: 16, fontWeight: '800', marginBottom: 12 },
  tile: {
    flexDirection: 'row',
    alignItems: 'center',
    borderRadius: 16,
    marginBottom: 10,
    padding: 14,
    borderWidth: 1,
    gap: 12,
  },
  tileIconWrap: {
    width: 40,
    height: 40,
    borderRadius: 12,
    alignItems: 'center',
    justifyContent: 'center',
  },
  tileDot: {
    width: 10,
    height: 10,
    borderRadius: 5,
    backgroundColor: PRIMARY,
  },
  tileBody: { flex: 1 },
  tileTitle: { fontSize: 15, fontWeight: '700' },
  tileSub: { fontSize: 12, marginTop: 3, lineHeight: 17 },
  actionCard: {
    flexDirection: 'row',
    alignItems: 'center',
    borderRadius: 18,
    padding: 16,
    marginBottom: 12,
    borderWidth: 1,
    shadowColor: '#0f172a',
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.06,
    shadowRadius: 14,
    elevation: 3,
  },
  pressed: { opacity: 0.92, transform: [{ scale: 0.995 }] },
  actionIconWrap: {
    width: 50,
    height: 50,
    borderRadius: 16,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 14,
  },
  actionBody: { flex: 1 },
  actionTitle: { fontSize: 16, fontWeight: '800' },
  actionSub: { fontSize: 13, marginTop: 4, lineHeight: 18 },
  actionArrow: {
    width: 32,
    height: 32,
    borderRadius: 16,
    alignItems: 'center',
    justifyContent: 'center',
  },
});
