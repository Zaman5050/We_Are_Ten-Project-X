import moment from "moment-timezone";

export const applyDateRangeFilter = (data, payload) => {
    const today = new Date();
    let startDate = null;
    let endDate = null;

    switch (payload.type) {
        case "thisYear":
            startDate = new Date(today.getFullYear(), 0, 1);
            endDate = new Date(today.getFullYear(), 11, 31);
            break;
        case "lastMonth":
            startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            endDate = new Date(today.getFullYear(), today.getMonth(), 0);
            break;
        case "customRange":
            startDate = new Date(payload.range.start);
            endDate = new Date(payload.range.end);
            break;
        default:
            return data;
    }

    // Filter data based on the computed startDate and endDate
    return data.filter((project) => {
        const createdDate = new Date(project.start_date);
        return createdDate >= startDate && createdDate <= endDate;
    });
};

export const computeTimezoneDate = (
    date,
    timezone = "UTC",
    format = "DD/MM/YYYY"
) => {
    return moment(date).tz(timezone, true).format(format);
};

