<h2>Edit Group Modul</h2>
<form method="post" action="<?= base_url('pengaturan/group_modul/update/' . $modul['ID']) ?>">
    <label>MODUL</label>
    <input type="text" name="MODUL" value="<?= esc($modul['GROUP_MODUL']) ?>" required>
    <br>
    <label>STATUS</label>
    <select name="STATUS">
        <option value="1" <?= $modul['STATUS'] == 1 ? 'selected' : '' ?>>Aktif</option>
        <option value="0" <?= $modul['STATUS'] == 0 ? 'selected' : '' ?>>Tidak Aktif</option>
    </select>
    <br>
    <button type="submit">Update</button>
</form>