<tr>
    <td colspan="2">{t}And where{/t}</td>
</tr>
<tr>
    <th>{t}The date is{/t}</th>
    <td>{html_select_date start_year='2007' field_order='Y' prefix='' field_array='params[report.from]' day_value_format='%02d' time=$formData.dob id='cohort'}
</td>
</tr>
<tr>
    <th>{t}The type is{/t}</th>
    <td>
        <select name="params[report.type]">
            <option value="Activity">Activity</option>
            <option value="Engage">Engagement</option>                
        </select>
    </td>
</tr>