import { useForm } from "@inertiajs/react";
import { useState } from "react";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ChoiceControl from "@/components/Forms/ChoiceControl/ChoiceControl";
import ChoiceGroup from "@/components/Forms/ChoiceGroup/ChoiceGroup";
import DatePicker from "@/components/Forms/DatePicker/DatePicker";
import EvidenceUpload from "@/components/Forms/EvidenceUpload/EvidenceUpload";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import GeoMap, { type GeoPoint } from "@/components/InclusiveRadar/GeoMap/GeoMap";
import { notify } from "@/components/UI/NotificationToast/notify";
import BarrierPage from "./BarrierPage";
import type { BarrierCategory, BarrierDeficiency, BarrierInstitution, BarrierOption, BarrierRecord } from "./Barrier.types";
import "./Barriers.css";

const base = "/inclusive-radar/barriers";
const defaultPoint: GeoPoint = { latitude: -14.235, longitude: -51.9253 };
const localToday = (() => {
    const now = new Date();
    return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, "0")}-${String(now.getDate()).padStart(2, "0")}`;
})();

type FormData = {
    name: string;
    description: string;
    institution_id: string;
    barrier_category_id: string;
    priority: string;
    identified_at: string;
    location_id: string;
    location_specific_details: string;
    latitude: string;
    longitude: string;
    no_location: boolean;
    is_anonymous: boolean;
    not_applicable: boolean;
    affected_student_id: string;
    affected_professional_id: string;
    affected_person_name: string;
    affected_person_role: string;
    deficiencies: string[];
    status: string;
    is_active: boolean;
    inspection: { date: string; type: string; description: string; evidences: File[] };
};

export default function BarrierForm({
    barrier,
    institutions,
    categories,
    deficiencies,
    students,
    professionals,
    priorities,
    barrierStatuses,
    selectedInstitution,
    canSubmit,
}: {
    barrier?: BarrierRecord;
    institutions: BarrierInstitution[];
    categories: BarrierCategory[];
    deficiencies: BarrierDeficiency[];
    students: Record<string, string>;
    professionals: Record<string, string>;
    priorities: BarrierOption[];
    barrierStatuses: BarrierOption[];
    selectedInstitution: BarrierInstitution | null;
    canSubmit: boolean;
}): React.JSX.Element {
    const editing = Boolean(barrier);
    const [showLocations, setShowLocations] = useState(true);
    const initialInstitutionId = String(selectedInstitution?.id ?? barrier?.institution_id ?? "");
    const initialStatus = barrier?.inspections?.[0]?.status ?? "identified";
    const initialPoint = barrier?.latitude !== null && barrier?.latitude !== undefined
        && barrier?.longitude !== null && barrier?.longitude !== undefined
        ? { latitude: barrier.latitude, longitude: barrier.longitude }
        : selectedInstitution ?? defaultPoint;
    const form = useForm<FormData>({
        name: barrier?.name ?? "",
        description: barrier?.description ?? "",
        institution_id: initialInstitutionId,
        barrier_category_id: String(barrier?.barrier_category_id ?? ""),
        priority: barrier?.priority ?? "medium",
        identified_at: barrier?.identified_at?.slice(0, 10) ?? localToday,
        location_id: String(barrier?.location_id ?? ""),
        location_specific_details: barrier?.location_specific_details ?? "",
        latitude: String(initialPoint.latitude),
        longitude: String(initialPoint.longitude),
        no_location: false,
        is_anonymous: barrier?.is_anonymous ?? false,
        not_applicable: barrier?.is_anonymous ? false : barrier?.not_applicable ?? false,
        affected_student_id: String(barrier?.affected_student_id ?? ""),
        affected_professional_id: String(barrier?.affected_professional_id ?? ""),
        affected_person_name: barrier?.affected_person_name ?? "",
        affected_person_role: barrier?.affected_person_role ?? "",
        deficiencies: barrier?.deficiencies?.map((item) => String(item.id)) ?? [],
        status: initialStatus,
        is_active: true,
        inspection: {
            date: localToday,
            type: editing ? "periodic" : "initial",
            description: "",
            evidences: [],
        },
    });
    const errors = form.errors as Record<string, string | undefined>;
    const institution = institutions.find((item) => item.id === Number(form.data.institution_id)) ?? null;
    const category = categories.find((item) => item.id === Number(form.data.barrier_category_id));
    const mapBlocked = category?.blocks_map ?? false;
    const mapPoint = {
        latitude: Number.isFinite(Number.parseFloat(form.data.latitude)) ? Number.parseFloat(form.data.latitude) : defaultPoint.latitude,
        longitude: Number.isFinite(Number.parseFloat(form.data.longitude)) ? Number.parseFloat(form.data.longitude) : defaultPoint.longitude,
    };
    const locationOptions = institution?.locations.map((location) => ({ value: String(location.id), label: location.name })) ?? [];
    const audiencesError = errors.deficiencies ?? errors["deficiencies.0"];
    const evidenceError = Object.entries(errors).find(([key]) => key.startsWith("inspection.evidences"))?.[1];

    function updatePoint(point: GeoPoint): void {
        form.setData((current) => ({ ...current, latitude: point.latitude.toFixed(8), longitude: point.longitude.toFixed(8) }));
    }

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        const options = {
            preserveScroll: true,
            onNetworkError: () => {
                notify({ tone: "error", title: "Falha de conexão", text: "Confira sua conexão e tente novamente." });
                return false;
            },
            onHttpException: () => {
                notify({ tone: "error", title: "Não foi possível concluir", text: "Não foi possível salvar a barreira. Tente novamente." });
                return false;
            },
        };
        if (barrier) form.put(`${base}/${barrier.id}`, options);
        else form.post(`${base}/store`, options);
    }

    return (
        <BarrierPage
            title={editing ? "Editar Barreira de Acessibilidade" : "Relatar Barreira de Acessibilidade"}
            subtitle={editing ? `Atualizando informações de ${barrier?.name ?? ""}.` : "Registre um ponto de obstrução ou dificuldade encontrada no campus."}
            current={editing ? "Editar" : "Cadastrar"}
            barrier={barrier}
        >
            <FormLayout onSubmit={submit} aria-busy={form.processing}>
                <CardBox>
                    <FormSection id="barrier-details" title="Detalhes da Ocorrência" variant="inline" iconClass="fa fa-exclamation-triangle">
                        <FormGrid columns={4}>
                            <FormField id="barrier-name" label="Título do Relato" requiredIndicator error={errors.name} className="barrierForm__name">
                                <TextInput id="barrier-name" name="name" required maxLength={255} value={form.data.name} invalid={!!errors.name} aria-describedby={errors.name ? "barrier-name-error" : undefined} disabled={form.processing} placeholder="Ex.: Calçada irregular" onChange={(event) => form.setData("name", event.target.value)} />
                            </FormField>
                            <FormField id="barrier-date" label="Data" requiredIndicator error={errors.identified_at}>
                                <DatePicker id="barrier-date" name="identified_at" label="Data" required value={form.data.identified_at} invalid={!!errors.identified_at} describedBy={errors.identified_at ? "barrier-date-error" : undefined} disabled={form.processing} onChange={(value) => form.setData("identified_at", value)} />
                            </FormField>
                            <FormField id="barrier-priority" label="Prioridade" requiredIndicator error={errors.priority}>
                                <SelectField id="barrier-priority" name="priority" label="Prioridade" required value={form.data.priority} options={priorities} disabled={form.processing} aria-invalid={!!errors.priority} aria-describedby={errors.priority ? "barrier-priority-error" : undefined} onChange={(event) => form.setData("priority", event.target.value)} />
                            </FormField>
                            <FormField id="barrier-category" label="Categoria" requiredIndicator error={errors.barrier_category_id}>
                                <SelectField id="barrier-category" name="barrier_category_id" label="Categoria" required value={form.data.barrier_category_id} options={categories.map((item) => ({ value: String(item.id), label: item.name }))} disabled={form.processing} aria-invalid={!!errors.barrier_category_id} aria-describedby={errors.barrier_category_id ? "barrier-category-error" : undefined} onChange={(event) => form.setData("barrier_category_id", event.target.value)} />
                            </FormField>
                            <FormField id="barrier-institution" label="Campus / Unidade" requiredIndicator error={errors.institution_id}>
                                <SelectField id="barrier-institution" name="institution_id" label="Campus / Unidade" required value={form.data.institution_id} options={institutions.map((item) => ({ value: String(item.id), label: item.name }))} disabled={form.processing} aria-invalid={!!errors.institution_id} aria-describedby={errors.institution_id ? "barrier-institution-error" : undefined} onChange={(event) => {
                                    const next = institutions.find((item) => item.id === Number(event.target.value));
                                    setShowLocations(true);
                                    form.setData((current) => ({ ...current, institution_id: event.target.value, location_id: "", latitude: String(next?.latitude ?? defaultPoint.latitude), longitude: String(next?.longitude ?? defaultPoint.longitude) }));
                                }} />
                            </FormField>
                            <FormField id="barrier-location" label="Local / Ponto de Referência" error={errors.location_id}>
                                <SelectField id="barrier-location" name="location_id" label="Local / Ponto de Referência" placeholder="Selecione um local..." value={form.data.location_id} options={locationOptions} disabled={!institution || form.processing} aria-invalid={!!errors.location_id} aria-describedby={errors.location_id ? "barrier-location-error" : undefined} onChange={(event) => {
                                    const selected = institution?.locations.find((item) => String(item.id) === event.target.value);
                                    form.setData((current) => ({ ...current, location_id: event.target.value, ...(selected ? { latitude: String(selected.latitude), longitude: String(selected.longitude) } : {}) }));
                                }} />
                            </FormField>
                            <FormGridWide>
                                <FormField id="barrier-location-details" label="Complemento" error={errors.location_specific_details}>
                                    <TextArea id="barrier-location-details" name="location_specific_details" rows={2} maxLength={255} value={form.data.location_specific_details} aria-invalid={!!errors.location_specific_details} aria-describedby={errors.location_specific_details ? "barrier-location-details-error" : undefined} disabled={form.processing} placeholder="Descreva melhor onde a barreira está localizada..." onChange={(event) => form.setData("location_specific_details", event.target.value)} />
                                </FormField>
                            </FormGridWide>
                            <FormGridWide>
                                <FormField id="barrier-description" label="Descrição Detalhada" requiredIndicator error={errors.description}>
                                    <TextArea id="barrier-description" name="description" required rows={3} value={form.data.description} aria-invalid={!!errors.description} aria-describedby={errors.description ? "barrier-description-error" : undefined} disabled={form.processing} placeholder="Explique o problema encontrado..." onChange={(event) => form.setData("description", event.target.value)} />
                                </FormField>
                            </FormGridWide>
                        </FormGrid>
                    </FormSection>

                    <FormSection id="barrier-people" title="Pessoa Impactada" variant="inline" iconClass="fa fa-users">
                        <div className="barrierForm__people">
                            <div className="formStack">
                                <ChoiceControl type="checkbox" id="barrier-anonymous" name="is_anonymous" label="Relato Anônimo" checked={form.data.is_anonymous} disabled={form.processing} onChange={(checked) => form.setData((current) => ({ ...current, is_anonymous: checked, not_applicable: checked ? false : current.not_applicable, affected_student_id: checked ? "" : current.affected_student_id, affected_professional_id: checked ? "" : current.affected_professional_id, affected_person_name: checked ? "" : current.affected_person_name, affected_person_role: checked ? "" : current.affected_person_role }))} />
                                {!form.data.is_anonymous ? <ChoiceControl type="checkbox" id="barrier-not-applicable" name="not_applicable" label="Relato Geral" checked={form.data.not_applicable} disabled={form.processing} onChange={(checked) => form.setData((current) => ({ ...current, not_applicable: checked, affected_student_id: checked ? "" : current.affected_student_id, affected_professional_id: checked ? "" : current.affected_professional_id, affected_person_name: checked ? current.affected_person_name : "", affected_person_role: checked ? current.affected_person_role : "" }))} /> : null}
                            </div>
                            {!form.data.is_anonymous && !form.data.not_applicable ? (
                                <FormGrid columns={2}>
                                    <FormField id="barrier-student" label="Estudante" error={errors.affected_student_id}>
                                        <SelectField id="barrier-student" name="affected_student_id" label="Estudante" placeholder="Selecione" value={form.data.affected_student_id} options={Object.entries(students).map(([value, label]) => ({ value, label }))} disabled={form.processing} aria-invalid={!!errors.affected_student_id} aria-describedby={errors.affected_student_id ? "barrier-student-error" : undefined} onChange={(event) => form.setData("affected_student_id", event.target.value)} />
                                    </FormField>
                                    <FormField id="barrier-professional" label="Profissional" error={errors.affected_professional_id}>
                                        <SelectField id="barrier-professional" name="affected_professional_id" label="Profissional" placeholder="Selecione" value={form.data.affected_professional_id} options={Object.entries(professionals).map(([value, label]) => ({ value, label }))} disabled={form.processing} aria-invalid={!!errors.affected_professional_id} aria-describedby={errors.affected_professional_id ? "barrier-professional-error" : undefined} onChange={(event) => form.setData("affected_professional_id", event.target.value)} />
                                    </FormField>
                                </FormGrid>
                            ) : null}
                            {!form.data.is_anonymous && form.data.not_applicable ? (
                                <FormGrid columns={2}>
                                    <FormField id="barrier-person-name" label="Nome" error={errors.affected_person_name}>
                                        <TextInput id="barrier-person-name" name="affected_person_name" maxLength={255} value={form.data.affected_person_name} invalid={!!errors.affected_person_name} aria-describedby={errors.affected_person_name ? "barrier-person-name-error" : undefined} disabled={form.processing} onChange={(event) => form.setData("affected_person_name", event.target.value)} />
                                    </FormField>
                                    <FormField id="barrier-person-role" label="Cargo" error={errors.affected_person_role}>
                                        <TextInput id="barrier-person-role" name="affected_person_role" maxLength={255} value={form.data.affected_person_role} invalid={!!errors.affected_person_role} aria-describedby={errors.affected_person_role ? "barrier-person-role-error" : undefined} disabled={form.processing} onChange={(event) => form.setData("affected_person_role", event.target.value)} />
                                    </FormField>
                                </FormGrid>
                            ) : null}
                        </div>
                    </FormSection>

                    <FormSection id="barrier-audience" title="Público Alvo" variant="inline" iconClass="fa fa-wheelchair">
                        <FormField id="barrier-audience-group" label="Público Alvo" requiredIndicator error={audiencesError}>
                            <ChoiceGroup id="barrier-audience-group" label="Selecione os públicos afetados" invalid={!!audiencesError} describedBy={audiencesError ? "barrier-audience-group-error" : undefined} className="barrierForm__choices">
                                {deficiencies.map((item) => (
                                    <ChoiceControl key={item.id} type="checkbox" id={`barrier-deficiency-${item.id}`} name="deficiencies[]" label={item.name} checked={form.data.deficiencies.includes(String(item.id))} disabled={form.processing} onChange={(checked) => form.setData((current) => ({ ...current, deficiencies: checked ? [...current.deficiencies, String(item.id)] : current.deficiencies.filter((id) => id !== String(item.id)) }))} />
                                ))}
                            </ChoiceGroup>
                        </FormField>
                    </FormSection>

                    <FormSection id="barrier-map" title="Localização no Mapa" variant="inline" iconClass="fa fa-map-marker">
                        <FormGrid columns={2}>
                            <FormGridWide>
                                <ChoiceControl
                                    id="barrier-show-locations"
                                    type="checkbox"
                                    label="Exibir Locais (Cinza)"
                                    checked={showLocations}
                                    disabled={form.processing}
                                    className="barrierMap__toggle"
                                    onChange={setShowLocations}
                                />
                                <div className="barrierForm__mapFrame">
                                    <GeoMap title="Localização da Barreira" value={mapPoint} zoom={editing ? 18 : institution?.default_zoom ?? 16} editable interactionEnabled={!!institution && !mapBlocked && !form.processing} markerLabel="Barreira selecionada" reference={institution} showReferenceMarkers={showLocations} onChange={updatePoint} onBlockedInteraction={() => {
                                        notify({ tone: "warning", title: mapBlocked ? "Mapa não aplicável" : "Selecione a instituição", text: mapBlocked ? `O mapa não se aplica à categoria ${category?.name ?? "selecionada"}.` : "Escolha uma instituição antes de marcar a localização." });
                                    }} />
                                    {errors.latitude || errors.longitude ? <p className="geoMap__message" role="alert">{errors.latitude ?? errors.longitude}</p> : null}
                                    {!institution || mapBlocked ? <div className="barrierForm__mapBlock"><span>{mapBlocked ? <>Mapa não se aplica à categoria<br />{category?.name}</> : "Selecione um campus ou unidade para localizar a barreira."}</span></div> : null}
                                </div>
                            </FormGridWide>
                        </FormGrid>
                    </FormSection>

                    <FormSection id="barrier-inspection" title={editing ? "Vistoria Periódica" : "Vistoria Inicial"} variant="inline" iconClass="fa fa-clipboard">
                        <FormGrid columns={2}>
                            <FormField id="barrier-status" label={editing ? "Status Atual" : "Status Inicial"} requiredIndicator={!editing} error={errors.status}>
                                <SelectField id="barrier-status" name="status" label="Status" required={!editing} value={form.data.status} options={barrierStatuses} disabled={form.processing} aria-invalid={!!errors.status} aria-describedby={errors.status ? "barrier-status-error" : undefined} onChange={(event) => form.setData("status", event.target.value)} />
                            </FormField>
                            <FormField id="barrier-inspection-date" label="Data da Vistoria" requiredIndicator error={errors["inspection.date"]}>
                                <DatePicker id="barrier-inspection-date" name="inspection[date]" label="Data da Vistoria" required value={form.data.inspection.date} invalid={!!errors["inspection.date"]} describedBy={errors["inspection.date"] ? "barrier-inspection-date-error" : undefined} disabled={form.processing} onChange={(value) => form.setData((current) => ({ ...current, inspection: { ...current.inspection, date: value } }))} />
                            </FormField>
                            <FormGridWide>
                                <EvidenceUpload id="barrier-evidences" name="inspection[evidences][]" files={form.data.inspection.evidences} onChange={(files) => form.setData((current) => ({ ...current, inspection: { ...current.inspection, evidences: files } }))} error={evidenceError} disabled={form.processing} />
                            </FormGridWide>
                            <FormGridWide>
                                <FormField id="barrier-inspection-description" label={editing ? "Parecer Técnico / Notas" : "Parecer Técnico / Notas da Vistoria"} error={errors["inspection.description"]}>
                                    <TextArea id="barrier-inspection-description" name="inspection[description]" rows={3} maxLength={1000} value={form.data.inspection.description} aria-invalid={!!errors["inspection.description"]} aria-describedby={errors["inspection.description"] ? "barrier-inspection-description-error" : undefined} disabled={form.processing} placeholder="Descreva detalhes técnicos sobre a obstrução ou estado do local..." onChange={(event) => form.setData((current) => ({ ...current, inspection: { ...current.inspection, description: event.target.value } }))} />
                                </FormField>
                            </FormGridWide>
                        </FormGrid>
                    </FormSection>
                    <input type="hidden" name="inspection[type]" value={form.data.inspection.type} />
                    <input type="hidden" name="no_location" value={String(form.data.no_location)} />
                    <input type="hidden" name="is_active" value="1" />
                </CardBox>
                <FormActions status={form.processing ? (editing ? "Salvando alterações…" : "Registrando barreira…") : undefined}>
                    <Button href={barrier ? `${base}/${barrier.id}` : base} tone="cancel" label="Cancelar" disabled={form.processing} />
                    {canSubmit ? <Button type="submit" tone="save" label="Salvar" disabled={form.processing} /> : null}
                </FormActions>
            </FormLayout>
        </BarrierPage>
    );
}
