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
import { PRIMARY, APP_TITLE } from '../config';
import { theme } from '../ui/theme';

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
  const header = (
    <View style={[styles.header, { paddingTop: insets.top + 8 }]}>
      <StatusBar barStyle="light-content" backgroundColor={PRIMARY} />
      <View style={styles.headerRow}>
        {onBack ? (
          <Pressable onPress={onBack} style={styles.backBtn}>
            <Text style={styles.backText}>←</Text>
          </Pressable>
        ) : (
          <View style={styles.backPlaceholder} />
        )}
        <View style={styles.headerCenter}>
          <Text style={styles.brand}>{APP_TITLE}</Text>
          <Text style={styles.title}>{title}</Text>
          {subtitle ? <Text style={styles.subtitle}>{subtitle}</Text> : null}
        </View>
        <View style={styles.right}>{rightAction ?? <View style={styles.backPlaceholder} />}</View>
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
  header: { backgroundColor: PRIMARY, paddingBottom: 16, paddingHorizontal: 12 },
  headerRow: { flexDirection: 'row', alignItems: 'center' },
  backBtn: { width: 40, height: 40, justifyContent: 'center', alignItems: 'center' },
  backText: { color: '#fff', fontSize: 22, fontWeight: '700' },
  backPlaceholder: { width: 40 },
  headerCenter: { flex: 1, alignItems: 'center' },
  brand: { color: 'rgba(255,255,255,0.8)', fontSize: 11, fontWeight: '600' },
  title: { color: '#fff', fontSize: 18, fontWeight: '800', marginTop: 2 },
  subtitle: { color: 'rgba(255,255,255,0.85)', fontSize: 12, marginTop: 2 },
  right: { width: 40, alignItems: 'flex-end' },
  body: { flex: 1 },
  scrollContent: { padding: 16, paddingBottom: 100 },
});
