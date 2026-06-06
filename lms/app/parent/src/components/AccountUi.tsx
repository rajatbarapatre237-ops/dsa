import React from 'react';
import { View, Text, StyleSheet, Pressable } from 'react-native';
import AppIcon from './AppIcon';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';

export function AccountProfileCard({
  portalLabel,
  title,
  subtitle,
  avatarLabel,
  chips,
}: {
  portalLabel: string;
  title: string;
  subtitle?: string;
  avatarLabel?: string;
  chips?: { label: string; icon?: string }[];
}) {
  const initial = (avatarLabel ?? title).trim().charAt(0).toUpperCase() || 'P';

  return (
    <View style={styles.profileCard}>
      <View style={styles.profileBanner}>
        <View style={styles.avatarRing}>
          <View style={styles.avatar}>
            <Text style={styles.avatarText}>{initial}</Text>
          </View>
        </View>
        <Text style={styles.portalLabel}>{portalLabel}</Text>
        <Text style={styles.profileTitle}>{title}</Text>
        {subtitle ? <Text style={styles.profileSub}>{subtitle}</Text> : null}
      </View>
      {chips && chips.length > 0 ? (
        <View style={styles.chipRow}>
          {chips.map(chip => (
            <View key={chip.label} style={styles.chip}>
              {chip.icon ? (
                <AppIcon name={chip.icon} size={14} color={PRIMARY} style={styles.chipIcon} />
              ) : null}
              <Text style={styles.chipText} numberOfLines={1}>
                {chip.label}
              </Text>
            </View>
          ))}
        </View>
      ) : null}
    </View>
  );
}

export function DangerActionCard({
  iconName,
  iconFamily = 'ionicons',
  title,
  subtitle,
  onPress,
}: {
  iconName: string;
  iconFamily?: 'ionicons' | 'material';
  title: string;
  subtitle: string;
  onPress: () => void;
}) {
  return (
    <Pressable
      style={({ pressed }) => [styles.dangerCard, pressed && styles.pressed]}
      onPress={onPress}>
      <View style={styles.dangerIconWrap}>
        <AppIcon name={iconName} family={iconFamily} size={24} color={theme.danger} />
      </View>
      <View style={styles.dangerBody}>
        <Text style={styles.dangerTitle}>{title}</Text>
        <Text style={styles.dangerSub}>{subtitle}</Text>
      </View>
      <View style={styles.dangerArrow}>
        <AppIcon name="chevron-forward" size={18} color={theme.danger} />
      </View>
    </Pressable>
  );
}

export function AppInfoFooter({
  appTitle,
  portalLabel,
  version,
}: {
  appTitle: string;
  portalLabel: string;
  version?: string;
}) {
  return (
    <View style={styles.footer}>
      <View style={styles.footerIcon}>
        <AppIcon name="school-outline" size={18} color={PRIMARY} />
      </View>
      <Text style={styles.footerTitle}>{appTitle}</Text>
      <Text style={styles.footerSub}>
        {portalLabel}
        {version ? ` · v${version}` : ''}
      </Text>
    </View>
  );
}

const styles = StyleSheet.create({
  profileCard: {
    backgroundColor: theme.card,
    borderRadius: 22,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: theme.border,
    overflow: 'hidden',
    shadowColor: theme.shadow,
    shadowOffset: { width: 0, height: 10 },
    shadowOpacity: 0.08,
    shadowRadius: 18,
    elevation: 4,
  },
  profileBanner: {
    backgroundColor: theme.primarySoft,
    alignItems: 'center',
    paddingTop: 24,
    paddingBottom: 20,
    paddingHorizontal: 18,
  },
  avatarRing: {
    padding: 4,
    borderRadius: 999,
    backgroundColor: 'rgba(255,255,255,0.8)',
    marginBottom: 12,
  },
  avatar: {
    width: 72,
    height: 72,
    borderRadius: 36,
    backgroundColor: theme.card,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 2,
    borderColor: theme.primaryMuted,
  },
  avatarText: { fontSize: 30, fontWeight: '800', color: PRIMARY },
  portalLabel: {
    fontSize: 11,
    fontWeight: '700',
    color: PRIMARY,
    textTransform: 'uppercase',
    letterSpacing: 0.8,
  },
  profileTitle: {
    fontSize: 22,
    fontWeight: '800',
    color: theme.text,
    marginTop: 6,
    textAlign: 'center',
  },
  profileSub: {
    fontSize: 13,
    color: theme.muted,
    marginTop: 6,
    textAlign: 'center',
    lineHeight: 18,
  },
  chipRow: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'center',
    gap: 8,
    padding: 16,
  },
  chip: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: theme.primaryMuted,
    borderRadius: 999,
    paddingHorizontal: 12,
    paddingVertical: 7,
    maxWidth: '100%',
  },
  chipIcon: { marginRight: 6 },
  chipText: { fontSize: 12, fontWeight: '700', color: PRIMARY },
  dangerCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#fff5f5',
    borderRadius: 18,
    padding: 16,
    marginBottom: 12,
    borderWidth: 1,
    borderColor: '#fecaca',
  },
  pressed: { opacity: 0.92, transform: [{ scale: 0.995 }] },
  dangerIconWrap: {
    width: 50,
    height: 50,
    borderRadius: 16,
    backgroundColor: '#fee2e2',
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 14,
  },
  dangerBody: { flex: 1 },
  dangerTitle: { fontSize: 16, fontWeight: '800', color: theme.danger },
  dangerSub: { fontSize: 13, color: '#b91c1c', marginTop: 4, lineHeight: 18 },
  dangerArrow: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: '#fee2e2',
    alignItems: 'center',
    justifyContent: 'center',
  },
  footer: {
    alignItems: 'center',
    paddingVertical: 24,
    marginTop: 8,
  },
  footerIcon: {
    width: 40,
    height: 40,
    borderRadius: 14,
    backgroundColor: theme.primarySoft,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 10,
  },
  footerTitle: { fontSize: 14, fontWeight: '800', color: theme.text },
  footerSub: { fontSize: 12, color: theme.muted, marginTop: 4 },
});
