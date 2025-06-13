import { format } from "date-fns";
import { nl } from "date-fns/locale";

export const parseApiDateTime = (dateTimeStr: string): Date => {
  return new Date(dateTimeStr);
};

export const formatDisplayTime = (date: Date | string): string => {
  const d = typeof date === "string" ? parseApiDateTime(date) : date;
  return format(d, "HH:mm");
};

export const formatDisplayDate = (date: Date | string): string => {
  const d = typeof date === "string" ? parseApiDateTime(date) : date;
  return format(d, "d MMM yyyy", { locale: nl });
};

export const formatDisplayDateTime = (date: Date | string): string => {
  const d = typeof date === "string" ? parseApiDateTime(date) : date;
  return format(d, "d MMM, HH:mm", { locale: nl });
};

export const isSameHour = (date1: Date, date2: Date): boolean => {
  return (
    date1.getFullYear() === date2.getFullYear() &&
    date1.getMonth() === date2.getMonth() &&
    date1.getDate() === date2.getDate() &&
    date1.getHours() === date2.getHours()
  );
};

export const isSameDay = (date1: Date, date2: Date): boolean => {
  return (
    date1.getFullYear() === date2.getFullYear() &&
    date1.getMonth() === date2.getMonth() &&
    date1.getDate() === date2.getDate()
  );
};
