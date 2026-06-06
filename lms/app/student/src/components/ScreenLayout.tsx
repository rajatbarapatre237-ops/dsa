import React from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  RefreshControl,
  Pressable,
  StatusBar,
} from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';
import AppIcon from './AppIcon';

type Props = {
  title: string;
  subtitle?: string;
  children: React.ReactNode;
  onBack?: () => void;
  refreshing?: boolean;
  onRefresh?: () => void;
  scroll?: boolean;
  rightAction?: React.ReactNode;
};

export default function ScreenLayout({
  title,
  subtitle,
  children,
  onBack,
  refreshing,
  onRefresh,
  scroll = true,
  rightAction,
}: Props) {
  const insets = useSafeAreaInsets();
  const showSubtitle = !!onBack && !!subtitle;

  const header = (
    <View style={[styles.header, { paddingTop: insets.top }]}>
      <StatusBar barStyle="dark-content" backgroundColor={theme.card} />
      <View style={styles.headerRow}>
        {onBack ? (
          <Pressable onPress={onBack} hitSlop={8} style={styles.backBtn}>
            <AppIcon name="chevron-back" size={24} color={PRIMARY} />
          </Pressable>
        ) : (
          <View style={styles.sideSlot} />
        )}
        <View style={styles.headerText}>
          <Text style={styles.title} numberOfLines={1}>
            {title}
          </Text>
          {showSubtitle ? (
            <Text style={styles.subtitle} numberOfLines={1}>
              {subtitle}
            </Text>
          ) : null}
        </View>
        <View style={styles.sideSlot}>{rightAction}</View>
      </View>
    </View>
  );

  if (!scroll) {
    return (
      <View style={styles.root}>
        {header}
        <View style={styles.body}>{children}</View>
      </View>
    );
  }

  return (
    <View style={styles.root}>
      {header}
      <ScrollView
        style={styles.body}
        contentContainerStyle={styles.scrollContent}
        refreshControl={
          onRefresh ? (
            <RefreshControl refreshing={!!refreshing} onRefresh={onRefresh} tintColor={PRIMARY} />
          ) : undefined
        }>
        {children}
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: theme.bg },
  header: {
    backgroundColor: theme.card,
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderBottomColor: theme.border,
    shadowColor: theme.shadow,
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.04,
    shadowRadius: 8,
    elevation: 2,
  },
  headerRow: {
    minHeight: 48,
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 8,
    paddingBottom: 8,
  },
  sideSlot: {
    width: 40,
    alignItems: 'center',
    justifyContent: 'center',
  },
  backBtn: {
    width: 40,
    height: 40,
    justifyContent: 'center',
    alignItems: 'center',
  },
  headerText: {
    flex: 1,
    alignItems: 'center',
    paddingHorizontal: 4,
  },
  title: {
    color: theme.text,
    fontSize: 18,
    fontWeight: '800',
    letterSpacing: -0.2,
  },
  subtitle: {
    color: theme.muted,
    fontSize: 12,
    marginTop: 1,
  },
  body: { flex: 1 },
  scrollContent: { padding: 16, paddingBottom: 100 },
});
