<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use SchoolERP\Repositories\SubjectRepository;

$repository = new SubjectRepository();

$passed = 0;
$failed = 0;

/**
 * Assert a condition.
 */
function assertTest(
    bool $condition,
    string $message
): void {
    global $passed, $failed;

    if ($condition) {
        $passed++;
        echo "PASSED: {$message}\n";
        return;
    }

    $failed++;
    echo "FAILED: {$message}\n";
}

/**
 * Generate unique test data.
 */
$suffix = date('YmdHis') . '_' . random_int(1000, 9999);

$subjectName = 'Integrity Test Subject ' . $suffix;
$subjectCode = 'ITS-' . substr($suffix, -8);

$updatedName = 'Updated Integrity Subject ' . $suffix;
$updatedCode = 'UIS-' . substr($suffix, -8);

$duplicateName = 'Duplicate Integrity Subject ' . $suffix;
$duplicateCode = 'DIS-' . substr($suffix, -8);

echo "SUBJECT INTEGRITY TEST\n";
echo "======================\n\n";

$createdId = null;

try {
    /*
     * --------------------------------------------------------------
     * 1. Create
     * --------------------------------------------------------------
     */

    $createdId = $repository->create([
        'name' => $subjectName,
        'code' => $subjectCode,
        'status' => 'active',
    ]);

    assertTest(
        is_int($createdId) && $createdId > 0,
        'Subject creation returns a valid inserted ID'
    );

    /*
     * --------------------------------------------------------------
     * 2. Find created subject
     * --------------------------------------------------------------
     */

    $subject = $repository->find((int) $createdId);

    assertTest(
        $subject !== null,
        'Created Subject can be retrieved'
    );

    if ($subject !== null) {
        assertTest(
            $subject->getAttribute('id') === (int) $createdId,
            'Retrieved Subject has the correct ID'
        );

        assertTest(
            $subject->getAttribute('name') === $subjectName,
            'Created Subject preserves its name'
        );

        assertTest(
            $subject->getAttribute('code') === $subjectCode,
            'Created Subject preserves its code'
        );

        assertTest(
            $subject->getAttribute('status') === 'active',
            'Created Subject has active status'
        );

        /*
         * ----------------------------------------------------------
         * Relationship integrity
         * ----------------------------------------------------------
         */

        $assignments = $subject->teacherAssignments()->get();

        assertTest(
            is_array($assignments),
            'Subject teacherAssignments relationship returns an array'
        );

        assertTest(
            count($assignments) === 0,
            'New test Subject has no teacher assignments'
        );
    }

    /*
     * --------------------------------------------------------------
     * 3. Active scope
     * --------------------------------------------------------------
     */

    $activeSubjects = $repository->active();

    $activeIds = array_map(
        static fn (array $row): int => (int) $row['id'],
        $activeSubjects
    );

    assertTest(
        in_array((int) $createdId, $activeIds, true),
        'Active Subject appears in the active Subject list'
    );

    /*
     * --------------------------------------------------------------
     * 4. Rename
     * --------------------------------------------------------------
     */

    $renamed = $repository->rename(
        (int) $createdId,
        '  ' . $updatedName . '  '
    );

    assertTest(
        $renamed === true,
        'Subject rename succeeds'
    );

    $subject = $repository->find((int) $createdId);

    assertTest(
        $subject !== null
            && $subject->getAttribute('name') === $updatedName,
        'Subject rename trims and persists the new name'
    );

    /*
     * --------------------------------------------------------------
     * 5. Full update
     * --------------------------------------------------------------
     */

    $updated = $repository->updateSubject(
        (int) $createdId,
        [
            'name' => $updatedName,
            'code' => $updatedCode,
            'status' => 'active',
        ]
    );

    assertTest(
        $updated === true,
        'Subject full update succeeds'
    );

    $subject = $repository->find((int) $createdId);

    assertTest(
        $subject !== null
            && $subject->getAttribute('code') === $updatedCode,
        'Subject update persists the new code'
    );

    /*
     * --------------------------------------------------------------
     * 6. Deactivate
     * --------------------------------------------------------------
     */

    $deactivated = $repository->deactivate(
        (int) $createdId
    );

    assertTest(
        $deactivated === true,
        'Subject deactivation succeeds'
    );

    $subject = $repository->find((int) $createdId);

    assertTest(
        $subject !== null
            && $subject->getAttribute('status') === 'inactive',
        'Subject status becomes inactive'
    );

    $activeSubjects = $repository->active();

    $activeIds = array_map(
        static fn (array $row): int => (int) $row['id'],
        $activeSubjects
    );

    assertTest(
        !in_array((int) $createdId, $activeIds, true),
        'Inactive Subject is excluded from the active Subject list'
    );

    /*
     * --------------------------------------------------------------
     * 7. Reactivate
     * --------------------------------------------------------------
     */

    $activated = $repository->activate(
        (int) $createdId
    );

    assertTest(
        $activated === true,
        'Subject activation succeeds'
    );

    $subject = $repository->find((int) $createdId);

    assertTest(
        $subject !== null
            && $subject->getAttribute('status') === 'active',
        'Subject status returns to active'
    );

    /*
     * --------------------------------------------------------------
     * 8. Duplicate-name integrity
     * --------------------------------------------------------------
     */

    $duplicateId = $repository->create([
        'name' => $duplicateName,
        'code' => $duplicateCode,
        'status' => 'active',
    ]);

    assertTest(
        is_int($duplicateId) && $duplicateId > 0,
        'Duplicate-test base Subject can be created'
    );

    $duplicateNameRejected = false;

    try {
        $repository->create([
            'name' => $duplicateName,
            'code' => 'DIFFERENT-' . substr($suffix, -6),
            'status' => 'active',
        ]);
    } catch (PDOException $exception) {
        $duplicateNameRejected = true;
    }

    assertTest(
        $duplicateNameRejected,
        'Duplicate Subject name is rejected by the database'
    );

    /*
     * --------------------------------------------------------------
     * 9. Duplicate-code integrity
     * --------------------------------------------------------------
     */

    $duplicateCodeRejected = false;

    try {
        $repository->create([
            'name' => 'Different Subject ' . $suffix,
            'code' => $duplicateCode,
            'status' => 'active',
        ]);
    } catch (PDOException $exception) {
        $duplicateCodeRejected = true;
    }

    assertTest(
        $duplicateCodeRejected,
        'Duplicate Subject code is rejected by the database'
    );

    /*
     * --------------------------------------------------------------
     * 10. Invalid record handling
     * --------------------------------------------------------------
     */

    assertTest(
        $repository->find(0) === null,
        'Finding Subject ID 0 returns null'
    );

    assertTest(
        $repository->find(-1) === null,
        'Finding negative Subject ID returns null'
    );

    assertTest(
        $repository->find(999999999) === null,
        'Finding nonexistent Subject returns null'
    );

    assertTest(
        $repository->updateSubject(
            999999999,
            [
                'name' => 'Should Not Exist',
                'code' => 'NOPE',
                'status' => 'active',
            ]
        ) === false,
        'Updating nonexistent Subject returns false'
    );

    assertTest(
        $repository->rename(
            999999999,
            'Should Not Exist'
        ) === false,
        'Renaming nonexistent Subject returns false'
    );

    assertTest(
        $repository->activate(999999999) === false,
        'Activating nonexistent Subject returns false'
    );

    assertTest(
        $repository->deactivate(999999999) === false,
        'Deactivating nonexistent Subject returns false'
    );

    /*
     * --------------------------------------------------------------
     * 11. Delete test records
     * --------------------------------------------------------------
     */

    $deleted = $repository->delete((int) $createdId);

    assertTest(
        $deleted === true,
        'Test Subject can be deleted when it has no assignments'
    );

    assertTest(
        $repository->find((int) $createdId) === null,
        'Deleted test Subject no longer exists'
    );

    if (isset($duplicateId) && is_int($duplicateId)) {
        $duplicateDeleted = $repository->delete($duplicateId);

        assertTest(
            $duplicateDeleted === true,
            'Duplicate-test Subject can be deleted'
        );
    }
} catch (Throwable $exception) {
    $failed++;

    echo "\nUNEXPECTED EXCEPTION:\n";
    echo get_class($exception) . ': ';
    echo $exception->getMessage() . "\n";
} finally {
    /*
     * Best-effort cleanup if a failure occurred before normal cleanup.
     */
    if (
        $createdId !== null
        && $repository->find((int) $createdId) !== null
    ) {
        try {
            $repository->delete((int) $createdId);
        } catch (Throwable) {
            // Ignore cleanup failures.
        }
    }

    if (
        isset($duplicateId)
        && is_int($duplicateId)
        && $repository->find($duplicateId) !== null
    ) {
        try {
            $repository->delete($duplicateId);
        } catch (Throwable) {
            // Ignore cleanup failures.
        }
    }
}

echo "\n";
echo "PASSED: {$passed}\n";
echo "FAILED: {$failed}\n";

if ($failed === 0) {
    echo "SUBJECT INTEGRITY TEST PASSED\n";
    exit(0);
}

echo "SUBJECT INTEGRITY TEST FAILED\n";
exit(1);