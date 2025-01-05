// records.js - DNS Record Management
const RecordManager = {
    loadZoneRecords(records) {
        let output = '';
        records.forEach((record, index) => {
            output += this.renderRecordRow(record, index === 0);
        });
        return output;
    },

    renderRecordRow(record, isFirst) {
        const zoneCell = isFirst ? 
            `<td class="zone">${record.zone}</td>` : 
            `<td class="zone" style="color:#aaa;">${record.zone}</td>`;

        return `
            <tr data-id="${record.id}">
                ${zoneCell}
                <td class="ttl">${record.ttl}</td>
                <td class="type">${record.type}</td>
                <td class="host">${record.host}</td>
                <td class="mx_pri">${record.mx_priority || ''}</td>
                <td class="data"><div class="data">${this.formatRecordData(record)}</div></td>
            </tr>
        `;
    },

    formatRecordData(record) {
        if (record.type === "SOA") {
            return `${record.primary_ns} ${record.resp_contact} ${record.serial}`;
        }
        return record.data;
    },

    loadRecord(id) {
        const record = StateManager.getState().records.find(rec => rec.id === id);
        if (record) {
            UIService.updateRecordForm(record);
            StateManager.setState({ currentRecord: record });
        }
    }
};


