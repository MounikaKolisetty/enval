import { Component } from '@angular/core';
import { MatIconModule } from '@angular/material/icon'; // Import MatIconModule
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';

@Component({
  selector: 'app-footer',
  standalone: true,
  imports: [MatIconModule,
            RouterOutlet, 
            RouterLink, 
            RouterLinkActive, 
  ],
  templateUrl: './footer.component.html',
  styleUrl: './footer.component.css'
})
export class FooterComponent {

}
