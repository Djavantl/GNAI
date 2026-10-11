export type Permissions = Record<string, boolean>;
export type StatusColor = 'success' | 'warning' | 'primary' | 'danger' | 'info';
export interface StudentOptions {
  permissions: Permissions;
  studentStatuses: Record<string, string>;
  statusColors: Record<string, StatusColor>;
}
export interface Person {
  id: number; name: string; document: string | null; birth_date: string | null;
  gender: string; gender_label: string; age: number | null; email: string | null;
  phone: string | null; address: string | null; photo: string | null; photo_url: string;
  address_html: string;
}
export interface Student {
  id: number; person: Person; registration: string; entry_date: string | null; status: string;
}
export interface StudentFormOptions extends StudentOptions {
  genders: Record<string, string>; defaultGender?: string; defaultStatus?: string;
}
export type StudentFilters = Record<'name' | 'phone' | 'email' | 'status' | 'course_id' | 'deficiency_id' | 'registration' | 'per_page', string>;
export interface StudentIndexProps extends StudentOptions {
  students: { data: Student[]; current_page: number; last_page: number; from: number | null; to: number | null; total: number };
  courses: Array<{ id: number; name: string }>;
  deficiencies: Array<{ id: number; name: string }>;
  filters: Partial<StudentFilters>;
}
export interface StudentRecord extends Student {
  student_courses: Array<{ id: number; course: { name: string } | null; academic_year: number; is_current: boolean }>;
  guardians?: Array<{ id: number; person: Person; relationship_label: string }>;
  deficiencies?: Array<{ id: number; name: string; severity_label: string | null }>;
  current_context?: { id: number; semester_label: string | null; evaluator: { person: { name: string } } | null } | null;
  documents?: Array<{ id: number; title: string; original_name: string; type_label: string; created_at: string }>;
  peis?: Array<{ id: number; semester_label: string | null; course: { name: string } | null; teacher_names: string[]; is_finished: boolean }>;
}
export interface AttendanceSession {
  session_date: string | null;
  professional: { person: { name: string } } | null;
}
export interface AeeEvaluation {
  id: number; is_present: boolean;
  aee_record: { duration: number | string | null; attendance_session: AttendanceSession | null } | null;
}
export interface PedagogicalRecord {
  id: number; duration: number | string; is_present: boolean;
  attendance_session: AttendanceSession | null; guardians: Array<{ id: number }>;
}
export interface StudentShowProps extends StudentOptions {
  student: StudentRecord; aeeEvaluations: AeeEvaluation[]; pedagogicalRecords: PedagogicalRecord[];
}
