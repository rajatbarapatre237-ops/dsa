import React from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  RefreshControl,
  Pressable,
  StatusBar,
  Platform,
  KeyboardAvoidingView,
} from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { PRIMARY } from '../config';
import { useThemeColors } from '../ui/useThemeColors';
import { platformWeight } from '../ui/typography';
import { useTabBarStyle } from '../navigation/useTabBarStyle';
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

const keyboardBehavior = Platform.OS === 'ios' ? 'padding' : 'height';

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
  const colors = useThemeColors();
  const { contentBottomPadding } = useTabBarStyle();
  const showSubtitle = !!onBack && !!subtitle;

  const header = (
    <View style={[styles.header, { paddingTop: insets.top, backgroundColor: colors.card, borderBottomColor: colors.border }]}>
      <StatusBar barStyle={colors.isDark ? 'light-content' : 'dark-content'} backgroundColor={colors.card} />
      <View style={styles.headerRow}>
        {onBack ? (
          <Pressable onPress={onBack} hitSlop={8} style={styles.backBtn}>
            <AppIcon name="chevron-back" size={24} color={PRIMARY} />
          </Pressable>
        ) : (
          <View style={styles.sideSlot} />
        )}
        <View style={styles.headerText}>
          <Text style={[styles.title, { color: colors.text }]} numberOfLines={1}>
            {title}
          </Text>
          {showSubtitle ? (
            <Text style={[styles.subtitle, { color: colors.muted }]} numberOfLines={1}>
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
      <View style={[styles.root, { backgroundColor: colors.bg }]}>
        {header}
        <KeyboardAvoidingView
          style={styles.flex}
          behavior={keyboardBehavior}
          keyboardVerticalOffset={Platform.OS === 'ios' ? 0 : 24}>
          <View style={styles.flex}>{children}</View>
        </KeyboardAvoidingView>
      </View>
    );
  }

  return (
    <View style={[styles.root, { backgroundColor: colors.bg }]}>
      {header}
      <KeyboardAvoidingView
        style={styles.flex}
        behavior={keyboardBehavior}
        keyboardVerticalOffset={Platform.OS === 'ios' ? 0 : 24}>
        <ScrollView
          style={styles.flex}
          contentContainerStyle={[styles.scrollContent, { paddingBottom: contentBottomPadding }]}
          keyboardShouldPersistTaps="handled"
          keyboardDismissMode="on-drag"
          refreshControl={
            onRefresh ? (
              <RefreshControl refreshing={!!refreshing} onRefresh={onRefresh} tintColor={PRIMARY} />
            ) : undefined
          }>
          {children}
        </ScrollView>
      </KeyboardAvoidingView>
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1 },
  flex: { flex: 1 },
  header: {
    borderBottomWidth: StyleSheet.hairlineWidth,
    shadowColor: '#0f172a',
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
    fontSize: 18,
    ...platformWeight('800'),
    letterSpacing: Platform.OS === 'android' ? 0 : -0.2,
  },
  subtitle: {
    fontSize: 12,
    marginTop: 1,
  },
  scrollContent: { padding: 16, flexGrow: 1 },
});
