export const helpTopics = [
    {
        id: 'getting-started',
        title: 'Create an Exam',
        icon: '🚀',
        content: `
            <p>On the dashboard/Exam page click on 'Create New Exam'</p>
            <ul>
                <li>Give your exam a name</li>
                <li>Add description and instructions.</li>
                <li>Select quiz type and inductry</li>
                <li>Add quiz restrictions and Schedule</li>
                <li>Add Grading System and Certificate Settings(if needed)</li>
                <li>NEXT: add questions directly or question pools</li>
            </ul>
        `
    },
    {
        id: 'share-exam-link',
        title: 'Share Exam link',
        icon: '🔗',
        content: `
            <p>Registered examinees will see the exam on their dashboard when it is assigned.</p>
            <p>You can also copy and share the link by going to:</p>
            <ul>
                <li>'Exam' on the menu</li>
                <li>Click on the exam name.</li>
                <li>Copy the link under the Share Quiz section.</li>
            </ul>
        `
    },
    
    {
        id: 'creating-exams',
        title: 'Add question pool to Exam',
        icon: '📝',
        content: `
            <p>Question pools can be added to one or more exams, you can choose the number of questions to be shown from a question pool</p>
            <ul>
                <li>From the menu, click on question pool</li>
                <li>Click on 'Create new pool'</li>
                <li>Enter a name for your pool, e.g. personality test, add a description, these are not visible to the participant.</li>
            </ul>
            <b>Add question pool to Exam</b>
            <ul>
                <li>Click on 'Exam' on the menu.</li>
                <li>Click on the exam name.</li>
                <li>Scroll down to the 'Question pool section'.</li>
                <li>Under 'Add exiting pool', select the pool you will like to include in the Exam.</li>
                <li>Enter the number of Questions to show from this pool(a pool can have 30 questions, you can choose to pick 10 at random).</li>
                <li>Click on add pool.</li>
            </ul>
        `
    }
    ,
    {
        id: 'add-question',
        title: 'Create/Add question to question pool & Exam',
        icon: '📋',
        content: `
            <p>
            You can choose to create questions yourself or use our A.i. tool to create questions fast. You can only use A.i. in question pools.
            You can also add questions directly to an exam without using the question pool.
            </p>
            <ul>
                <li>From the menu, click on 'Question pool'</li>
                <li>Click 'Manage' on the question pool you will like to work on.</li>
                <li>On this page you have two buttons (2 options), you can choose to 'generate questions with AI' or 'Add question' from scratch.</li>
            </ul>
            <b>Add question without AI</b>
            <ul>
                <li>Click on 'Add Question'</li>
                <li>Select question type</li>
                <li>Enter the question in the 'Question text' box.</li>
                <li>Enter a description(optional).</li>
                <li>Enter the points for the question.</li>
                <li>Fill in other input and correct answers.</li>
                <li>Click 'Create Question'.</li>
            </ul>
            <b>Generate questions with AI</b>
            <ul>
                <li>On this page, you select either generating for a topic or from an article(which you will copy and paste in the text box provided).</li>
                <li>Enter the topic or article in the provided space.</li>
                <li>Next choose the question type or you can choose to randomize this.</li>
                <li>Enter the number of questions you will like to generate. Note that more questions will require more time.</li>
                <li>Select the difficulty and enter a language.</li>
                <li>Click the 'Generate question' button.</li>
            </ul>
            <p>The generated question will be displayed on the right, you can review these questions, delete the ones you do not want or delete all and regenerate.
                If you like the questions, click on 'Save to pool' and the questions will be added to the pool.</p>
        `
    },
    {
        id: 'managing-users',
        title: 'Managing Members',
        icon: '👥',
        content: `
            <p>You will be able to add examinees, examiners and instructors(depends on your plan). You will be able to add them directly
            by filling the form, importing form excel or CVS file and email invite. Examinees should always belong to a user group.</p>
            
            <b>Create Examinee group(s)</b>
            <ul>
                <li>Click on 'Examinee Group' form the menu.</li>
                <li>Click on 'Create New Group'.</li>
                <li>Give your examinee group a name(e.g. May recruits 2025, JSS 1A etc).</li>
                <li>Click 'Create Group'</li>
            </ul>
            <b>Add Examinees directly</b>
            <ul>
                <li>Click on 'Examinee' form the menu.</li>
                <li>Click on 'Add User'.</li>
                <li>Fill in the form.</li>
                <li>Click 'Create User'</li>
            </ul>
            <b>Import Examinees</b>
            <p>Imported members will be imported as examinees and will be registered, their password will be their full name, lowercase and no space.</p>
            <ul>
                <li>The excel or cvs file should have two columns 'name' and 'email'</li>
                <li>Click on choose file to select the file.</li>
                <li>Select the user timezone.</li>
                <li>Click import.</li>
            </ul>
            <b>Share link for group registration</b>
            <ul>
                <li>Click on 'Examinee Group' form the menu.</li>
                <li>Click 'Manage' on the group you will like to manage.</li>
                <li>Click on 'Share registration link' on the top right.</li>
                <li>You can copy and share the link or enter emails separated by commas to invite members to sign up to that group.</li>
            </ul>
        `
    },
    {
        id: 'assign-exam-to-group',
        title: 'Assign Exam to group',
        icon: '➕',
        content: `
            <p>Registered examinees will see the exam on their dashboard when it is assigned.</p>
            <p>You can also copy and share the link by going to:</p>
            <ul>
                <li>'Exam' on the menu</li>
                <li>Click on the exam name.</li>
                <li>Copy the link under the Share Quiz section.</li>
            </ul>
        `
    },
    {
        id: 'certificates',
        title: 'Certificates',
        icon: '🏆',
        content: `
            <p>Automate certificate issuance:</p>
            <ul>
                <li>Create certificate templates</li>
                <li>Set automatic certificate rules</li>
                <li>Customize certificate design</li>
                <li>Track issued certificates</li>
            </ul>
        `
    },
    {
        id: 'analytics',
        title: 'Analytics & Reports',
        icon: '📊',
        content: `
            <p>Understand your data:</p>
            <ul>
                <li>View performance analytics</li>
                <li>Generate detailed reports</li>
                <li>Track group vs individual performance</li>
                <li>Export data for further analysis</li>
            </ul>
        `
    },
    {
        id: 'settings',
        title: 'Settings',
        icon: '🔧',
        content: `
            <p>Common issues and solutions:</p>
            <ul>
                <li>Exam not starting properly</li>
                <li>User access issues</li>
                <li>Certificate generation problems</li>
                <li>Performance and loading issues</li>
            </ul>
        `
    }
];